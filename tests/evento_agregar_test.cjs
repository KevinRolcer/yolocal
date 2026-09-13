const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

async function verificarEnvio(respuesta, errorConexion = false) {
    const listeners = {};
    const alerts = [];
    const requests = [];
    let resets = 0;
    let hides = 0;
    const button = { disabled: false };
    const fields = { TituloE: 'Feria local', DescripcionE: 'Concierto', PrecioE: 'Gratis', FechaE: '2026-10-10', HoraE: '18:00', UbicacionE: 'Centro', Telefono: '2481234567', ID_Categoria: '1', RutaImagenE: new Blob(['imagen']) };
    const form = {
        fields,
        addEventListener: (name, listener) => { listeners[name] = listener; },
        querySelector: () => button,
        reset: () => { resets++; },
    };
    class TestFormData extends FormData {
        constructor(element) {
            super();
            for (const [key, value] of Object.entries(element?.fields || {})) this.append(key, value);
        }
    }
    const context = vm.createContext({
        window: { YL_controladorEventosUrl: () => '/yolocal/controladores/controladorEventos.php' },
        document: { readyState: 'complete', getElementById: id => id === 'formEvento' ? form : id === 'modalEvento' ? {} : null },
        FormData: TestFormData,
        console: { error() {} },
        bootstrap: { Modal: { getInstance: () => ({ hide: () => { hides++; } }) } },
        Swal: { fire: options => { alerts.push(options); } },
        fetch: async (url, options) => {
            requests.push({ url, ...options });
            if (options.body.get('ope') === 'AGREGAR' && errorConexion) throw new Error('Sin conexión');
            return { ok: true, text: async () => JSON.stringify(options.body.get('ope') === 'AGREGAR' ? respuesta : { success: true, lista: [] }) };
        },
    });
    vm.runInContext(fs.readFileSync('assets/js/funcionesEventos.js', 'utf8'), context);
    let prevented = false;
    listeners.submit({ preventDefault: () => { prevented = true; } });
    await new Promise(resolve => setImmediate(resolve));
    assert.equal(prevented, true);
    const request = requests.find(item => item.body.get('ope') === 'AGREGAR');
    assert.ok(request, 'El formulario debe enviar AGREGAR');
    assert.equal(request.url, '/yolocal/controladores/controladorEventos.php');
    assert.equal(request.method, 'POST');
    for (const [key, value] of Object.entries(fields)) {
        if (value instanceof Blob) assert.equal(await request.body.get(key).text(), 'imagen');
        else assert.equal(request.body.get(key), value);
    }
    const success = respuesta.success && !errorConexion;
    assert.equal(resets, success ? 1 : 0);
    assert.equal(hides, success ? 1 : 0);
    assert.equal(requests.filter(item => item.body.get('ope') === 'LISTAR').length, success ? 2 : 1);
    assert.equal(alerts.at(-1).icon, success ? 'success' : 'error');
    if (!success) assert.equal(alerts.at(-1).text, errorConexion ? 'Sin conexión' : respuesta.message);
    assert.equal(button.disabled, false);
}

(async () => {
    await verificarEnvio({ success: true });
    await verificarEnvio({ success: false, message: 'No se pudo guardar el evento.' });
    await verificarEnvio({ success: false }, true);
    console.log('PASS: envío con imagen, actualización del listado y conservación del formulario ante errores.');
})().catch(error => { console.error(error); process.exitCode = 1; });
