const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

async function ejecutarGuardado(respuesta) {
    const alertas = [];
    const solicitudes = [];
    let resets = 0;
    let cierres = 0;
    let actualizaciones = 0;
    const campos = {
        ID_Negocio: '17',
        dia_semana: 'Lunes',
        hora_apertura: '09:00',
        hora_cierre: '18:00',
    };
    const formulario = { reset: () => { resets++; } };
    class FormDataPrueba {
        constructor(form) {
            this.valores = new Map(Object.entries(form === formulario ? campos : {}));
        }
        append(nombre, valor) {
            this.valores.set(nombre, valor);
        }
        get(nombre) {
            return this.valores.get(nombre);
        }
    }

    const contexto = vm.createContext({
        document: {
            querySelector(selector) {
                if (selector === '#formHorario') return formulario;
                if (selector === '#modalHorario .btn-close') return { click: () => { cierres++; } };
                return null;
            },
        },
        FormData: FormDataPrueba,
        Swal: {
            fire(...argumentos) {
                alertas.push(argumentos);
                return Promise.resolve({ isConfirmed: true });
            },
        },
        fetch: async (url, opciones) => {
            solicitudes.push({ url, opciones });
            return { json: async () => respuesta };
        },
        listarMiembros: () => { actualizaciones++; },
    });

    const fuente = fs.readFileSync('assets/js/funcionesNegocio.js', 'utf8');
    const coincidencia = fuente.match(/function agregarHorario\(id\) \{[\s\S]*?\n\}/);
    assert.ok(coincidencia, 'Debe existir la función de guardado de horarios');
    vm.runInContext(coincidencia[0], contexto);
    vm.runInContext('agregarHorario()', contexto);
    await new Promise(resolve => setImmediate(resolve));

    return { alertas, solicitudes, resets, cierres, actualizaciones };
}

(async () => {
    const error = await ejecutarGuardado({ success: false, msg: 'El horario no se pudo guardar.' });
    assert.deepEqual(error.alertas.at(-1), ['Error', 'El horario no se pudo guardar.', 'error']);
    assert.equal(error.resets, 0);
    assert.equal(error.cierres, 0);

    const exito = await ejecutarGuardado({ success: true });
    assert.deepEqual(exito.alertas.at(-1), ['Guardado', 'Horario creado correctamente', 'success']);
    assert.equal(exito.solicitudes[0].url, 'controladores/controladorHorarios.php');
    assert.equal(exito.solicitudes[0].opciones.body.get('ope'), 'AGREGAR_HORARIO');
    assert.equal(exito.solicitudes[0].opciones.body.get('ID_Negocio'), '17');
    assert.equal(exito.resets, 1);
    assert.equal(exito.cierres, 1);
    assert.equal(exito.actualizaciones, 1);

    console.log('PASS: el guardado muestra errores del endpoint y completa el flujo al guardar.');
})().catch(error => {
    console.error(error);
    process.exitCode = 1;
});