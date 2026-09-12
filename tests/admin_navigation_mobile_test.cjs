const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const { spawnSync } = require('node:child_process');

const root = path.resolve(__dirname, '..');
const read = (file) => fs.readFileSync(path.join(root, file), 'utf8');
const modules = ['home', 'negocios', 'usuarios', 'cupones', 'categorias', 'bolsa_trabajo', 'eventos', 'pagos_admin'];
const sidebarMarkup = read('vistas/sistemaAdmin/encabezado.php');
const closeButton = sidebarMarkup.match(/<button\b[^>]*class="admin-sidebar-close"[\s\S]*?<\/button>/)?.[0] || '';
const cases = [];
const head = spawnSync('C:/xampp/php/php.exe', ['-r', 'require $argv[1];', path.join(root, 'vistas/sistemaAdmin/head.php')], { encoding: 'utf8' });
const sharedScripts = [...head.stdout.matchAll(/<script\b[^>]*src="([^"]+)"[^>]*>/g)]
  .filter((match) => /\/assets\/js\/main\.js(?:\?|$)/.test(match[1]))
  .map(() => `<script>${read('assets/js/main.js')}</script>`).join('');

for (const module of modules) {
  const view = read(`vistas/sistemaAdmin/${module}.php`);
  const bodyClass = view.match(/<body\b[^>]*class="([^"]*)"/)?.[1] || '';
  const files = [...view.match(/\$adminCssFiles\s*=\s*\[([^\]]+)\]/)[1].matchAll(/"([^"]+)"/g)].map((match) => match[1]);
  const css = ['assets/css/menu.css', ...files].map(read).join('\n').replace(/@import[^;]+;/g, '');
  for (const width of [390, 768, 1280]) {
    const assertions = `
      function check(condition, message) { if (!condition) throw new Error(message); }
      try {
        const toggle = document.querySelector('.topbar .toggle');
        const navigation = document.querySelector('.navigation');
        const main = document.querySelector('.main');
        const sidebar = document.querySelector('.sidebar');
        const mobile = innerWidth <= 991;
        check(toggle.tagName === 'BUTTON', 'El control de apertura debe ser un botón accesible');
        check(toggle.querySelector('svg'), 'El icono debe funcionar sin fuentes externas');
        if (mobile) {
          check(Math.abs(main.getBoundingClientRect().left) < 1, 'El contenido debe iniciar en el borde izquierdo');
          check(Math.abs(main.getBoundingClientRect().width - innerWidth) < 1, 'El contenido debe ocupar el ancho móvil');
          check(sidebar.inert, 'El menú cerrado no debe recibir foco');
        }
        toggle.click();
        check(navigation.classList.contains('active'), 'El botón debe abrir o contraer el menú');
        if (mobile) {
          const close = document.querySelector('.admin-sidebar-close');
          check(close && getComputedStyle(close).display !== 'none', 'Falta el botón visible para cerrar');
          const box = close.getBoundingClientRect();
          check(box.width >= 44 && box.height >= 44, 'El cierre debe tener área táctil suficiente');
          check(box.left >= 0 && box.right <= innerWidth, 'El cierre debe estar dentro de pantalla');
          check(close.contains(document.elementFromPoint(box.x + box.width / 2, box.y + box.height / 2)), 'El cierre no debe quedar cubierto');
          check(toggle.getAttribute('aria-expanded') === 'true', 'Debe anunciar el menú abierto');
          check(main.inert, 'El contenido detrás del menú no debe recibir foco');
          close.click();
          check(!navigation.classList.contains('active') && document.activeElement === toggle, 'Cerrar debe devolver el foco');
          toggle.click();
          document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
          check(!navigation.classList.contains('active'), 'Escape debe cerrar');
          toggle.click();
          const backdrop = document.querySelector('.admin-sidebar-backdrop');
          check(backdrop && !backdrop.hidden, 'Debe mostrar fondo para cerrar');
          backdrop.click();
          check(!navigation.classList.contains('active'), 'Tocar fuera debe cerrar');
          toggle.click();
          document.querySelector('.main-menu a').click();
          check(!navigation.classList.contains('active'), 'Cambiar módulo debe cerrar el menú');
        } else {
          check(!main.inert && !sidebar.inert, 'En escritorio debe permanecer accesible');
          toggle.click();
          check(!navigation.classList.contains('active'), 'Debe restaurar el menú de escritorio');
        }
        parent.postMessage({ ok: true, name: ${JSON.stringify(`${module} ${width}px`)} }, '*');
      } catch (error) {
        parent.postMessage({ ok: false, name: ${JSON.stringify(`${module} ${width}px`)}, error: error.message }, '*');
      }
    `;
    cases.push({ width, html: `<!doctype html><html><head><style>${css}</style><style>* { transition: none !important; animation: none !important; }</style></head><body class="${bodyClass}">
      <div class="navigation admin-sidebar"><div class="sidebar"><div class="logo-top"><span class="icon">YL</span><span class="title">Yo Local</span>${closeButton}</div><ul class="main-menu"><li><a href="#modulo">Inicio</a></li></ul></div></div>
      <div class="main"><div class="topbar"><div class="toggle"><i class="admin-menu-icon"></i></div></div><p>Contenido</p></div>
      <script>document.addEventListener('click', event => { if(event.target.closest('a')) event.preventDefault(); });</script>
      ${sharedScripts}<script>${assertions}</script></body></html>` });
  }
}

const directory = fs.mkdtempSync(path.join(os.tmpdir(), 'yolocal-navigation-'));
const fixture = path.join(directory, 'test.html');
fs.writeFileSync(fixture, `<!doctype html><html><body><pre id="result">PENDING</pre><script>
  const cases = ${JSON.stringify(cases).replace(/<\//g, '<\\/')};
  const results = [];
  addEventListener('message', event => {
    if (!event.data || typeof event.data.ok !== 'boolean') return;
    results.push(event.data);
    if (results.length === cases.length) document.getElementById('result').textContent = JSON.stringify(results);
  });
  for (const item of cases) {
    const frame = document.createElement('iframe');
    frame.style.cssText = 'display:block;border:0;height:844px;width:' + item.width + 'px';
    frame.srcdoc = item.html;
    document.body.appendChild(frame);
  }
</script></body></html>`);

const browser = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const result = spawnSync(browser, ['--headless=new', '--disable-gpu', '--no-first-run', '--no-default-browser-check', `--user-data-dir=${path.join(directory, 'profile')}`, '--dump-dom', '--virtual-time-budget=8000', `file:///${fixture.replace(/\\/g, '/')}`], { encoding: 'utf8', timeout: 45000, maxBuffer: 8 * 1024 * 1024 });
const output = result.stdout?.match(/<pre id="result">([^<]+)<\/pre>/)?.[1];
if (!output || output === 'PENDING') {
  console.error(result.error?.message || result.stderr || 'El navegador no terminó las pruebas.');
  process.exitCode = 1;
} else {
  const results = JSON.parse(output.replace(/&quot;/g, '"').replace(/&amp;/g, '&'));
  for (const item of results) console.log(`${item.ok ? 'PASS' : 'FAIL'}: ${item.name}${item.error ? ' — ' + item.error : ''}`);
  process.exitCode = results.every((item) => item.ok) ? 0 : 1;
}
