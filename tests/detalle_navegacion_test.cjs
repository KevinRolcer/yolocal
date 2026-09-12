const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const { spawnSync } = require('node:child_process');
const { pathToFileURL } = require('node:url');

const root = path.resolve(__dirname, '..');
const read = file => fs.readFileSync(path.join(root, file), 'utf8');
const header = read('vistas/header.php').replace('../assets/img/LogoYolocal.png', pathToFileURL(path.join(root, 'assets/img/LogoYolocal.png')).href);
const detail = read('vistas/detalle_negocio_vista.php');
const stripImports = css => css.replace(/@import\s+url\([\s\S]*?\)\s*;/g, '');
const script = read('assets/js/menuCl.js');
const frames = [];
for (const width of [390, 768, 1280]) {
  for (const variant of ['catalogo', 'detalle']) {
    const css = stripImports(read('assets/css/negociosCl.css') + (variant === 'detalle' ? read('assets/css/negocioD.css') : ''));
    const scriptCount = variant === 'detalle' ? [...detail.matchAll(/<script\b[^>]*src="[^"]*\/menuCl\.js(?:\?[^"]*)?"/g)].length : 1;
    frames.push({ width, html: `<!doctype html><html><head><style>${css}</style><style>* { transition:none !important; animation:none !important; }</style></head><body>
      <header class="encabezado">${header}</header>
      <script>const errors = []; addEventListener('error', event => errors.push(event.message));</script>
      ${Array.from({ length: scriptCount }, () => '<script>' + script + '</script>').join('')}
      <script>
        addEventListener('load', () => {
          const snapshot = {};
          const selectors = ['.encabezado', '.navbar', '.logo img', '.menu', '.enlace', '.sesion', '.btn-prueba', '.btn-sesion', '.menu-toggle'];
          const properties = ['display','position','padding','gap','fontSize','lineHeight','backgroundColor','backgroundImage','borderRadius','boxSizing','height','width'];
          for (const selector of selectors) {
            const style = getComputedStyle(document.querySelector(selector));
            snapshot[selector] = Object.fromEntries(properties.map(property => [property, style[property]]));
          }
          document.getElementById('menuToggle').click();
          snapshot.openPosition = getComputedStyle(document.getElementById('mainMenu')).position;
          snapshot.openDisplay = getComputedStyle(document.getElementById('mainMenu')).display;
          snapshot.open = document.getElementById('mainMenu').classList.contains('active');
          document.dispatchEvent(new KeyboardEvent('keydown', { key:'Escape', bubbles:true }));
          snapshot.closed = !document.getElementById('mainMenu').classList.contains('active');
          parent.postMessage({ width:${width}, variant:${JSON.stringify(variant)}, snapshot, errors }, '*');
        });
      </script></body></html>` });
  }
}
const directory = fs.mkdtempSync(path.join(os.tmpdir(), 'yolocal-detail-nav-'));
const fixture = path.join(directory, 'test.html');
fs.writeFileSync(fixture, `<!doctype html><html><body><pre id="result">PENDING</pre><script>
  const results = []; const cases = ${JSON.stringify(frames).replace(/<\//g, '<\\/')};
  addEventListener('message', event => {
    if (!event.data?.snapshot) return;
    results.push(event.data);
    if (results.length !== cases.length) return;
    const checks = [390,768,1280].map(width => {
      const reference = results.find(result => result.width === width && result.variant === 'catalogo');
      const detail = results.find(result => result.width === width && result.variant === 'detalle');
      const differences = Object.keys(reference.snapshot).filter(key => JSON.stringify(reference.snapshot[key]) !== JSON.stringify(detail.snapshot[key]));
      const values = Object.fromEntries(differences.map(key => [key, { expected:reference.snapshot[key], actual:detail.snapshot[key] }]));
      return { width, ok: !differences.length && !detail.errors.length, differences, values, errors:detail.errors };
    });
    document.getElementById('result').textContent = JSON.stringify(checks);
  });
  for (const item of cases) {
    const frame = document.createElement('iframe'); frame.style.cssText = 'border:0;height:844px;width:' + item.width + 'px';
    frame.srcdoc = item.html; document.body.appendChild(frame);
  }
</script></body></html>`);
const result = spawnSync(process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe', [
  '--headless=new','--disable-gpu','--no-first-run','--no-default-browser-check',`--user-data-dir=${path.join(directory, 'profile')}`,
  '--dump-dom','--virtual-time-budget=4000',`file:///${fixture.replace(/\\/g, '/')}`
], { encoding:'utf8', timeout:45000, maxBuffer:8 * 1024 * 1024 });
const output = result.stdout?.match(/<pre id="result">([^<]+)<\/pre>/)?.[1];
if (!output || output === 'PENDING') {
  console.error(result.error?.message || result.stderr); process.exitCode = 1;
} else {
  const checks = JSON.parse(output.replace(/&quot;/g, '"').replace(/&amp;/g, '&'));
  checks.forEach(check => console.log(`${check.ok ? 'PASS' : 'FAIL'}: ${check.width}px ${JSON.stringify({ differences:check.values, errors:check.errors })}`));
  process.exitCode = checks.every(check => check.ok) ? 0 : 1;
}
