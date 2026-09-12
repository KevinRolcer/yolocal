const fs = require('node:fs');
const path = require('node:path');
const os = require('node:os');
const { spawnSync } = require('node:child_process');

const root = path.resolve(__dirname, '..');
const read = file => fs.readFileSync(path.join(root, file), 'utf8');
const rendered = spawnSync('C:/xampp/php/php.exe', [path.join(__dirname, 'negocios_paginacion_test.php'), '--render'], { encoding: 'utf8' });
if (rendered.status !== 0) throw new Error(rendered.stderr);
const pagination = rendered.stdout.match(/<nav class="paginacion"[\s\S]*?<\/nav>/)[0];
const css = [read('assets/css/negociosCl.css'), read('assets/css/negocioL.css')].join('\n').replace(/@import\s+url\([\s\S]*?\)\s*;/g, '');
const widths = [320, 390, 768, 1280];
const frames = widths.map(width => ({ width, html: `<!doctype html><html><head><style>${css}</style></head><body class="catalogo-negocios">
  ${pagination}
  <script>
    try {
      const nav = document.querySelector('.paginacion');
      const check = (condition, message) => { if (!condition) throw new Error(message); };
      check(nav.getBoundingClientRect().right <= innerWidth && nav.scrollWidth <= nav.clientWidth, 'La barra se desborda');
      const current = nav.querySelector('[aria-current="page"]');
      check(getComputedStyle(current).backgroundColor === 'rgb(76, 6, 130)', 'La página actual debe estar resaltada');
      for (const link of nav.querySelectorAll('a')) {
        const box = link.getBoundingClientRect();
        check(box.width >= 44 && box.height >= 44, 'Área táctil insuficiente');
        check(box.left >= 0 && box.right <= innerWidth, 'Un enlace queda fuera de pantalla');
      }
      const previous = nav.querySelector('[rel="prev"]').getBoundingClientRect();
      const next = nav.querySelector('[rel="next"]').getBoundingClientRect();
      check(Math.abs(previous.top - next.top) < 1, 'Las flechas deben estar alineadas');
      if (innerWidth <= 640) check(previous.top > current.getBoundingClientRect().bottom, 'En móvil los controles deben distribuirse en dos filas');
      nav.querySelector('a').focus();
      check(getComputedStyle(document.activeElement).outlineStyle !== 'none', 'El foco de teclado debe ser visible');
      parent.postMessage({ width: ${width}, ok: true }, '*');
    } catch(error) { parent.postMessage({ width: ${width}, ok: false, error: error.message }, '*'); }
  </script></body></html>` }));
const directory = fs.mkdtempSync(path.join(os.tmpdir(), 'yolocal-pagination-'));
const fixture = path.join(directory, 'pagination.html');
fs.writeFileSync(fixture, `<!doctype html><html><body style="margin:24px;background:#f7f5f9;font:16px sans-serif"><pre id="result">PENDING</pre><script>
  const results = [];
  const cases = ${JSON.stringify(frames).replace(/<\//g, '<\\/')};
  addEventListener('message', event => {
    if (typeof event.data?.ok !== 'boolean') return;
    results.push(event.data);
    if (results.length === cases.length) document.getElementById('result').textContent = JSON.stringify(results);
  });
  for (const item of cases) {
    const label = document.createElement('p'); label.textContent = item.width + ' px'; document.body.appendChild(label);
    const frame = document.createElement('iframe'); frame.style.cssText = 'display:block;border:0;height:230px;width:' + item.width + 'px';
    frame.srcdoc = item.html; document.body.appendChild(frame);
  }
</script></body></html>`);
const screenshot = path.join(directory, 'pagination.png');
const browser = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const result = spawnSync(browser, ['--headless=new', '--disable-gpu', '--no-first-run', '--no-default-browser-check', `--user-data-dir=${path.join(directory, 'profile')}`, '--window-size=1400,1400', `--screenshot=${screenshot}`, '--dump-dom', '--virtual-time-budget=4000', `file:///${fixture.replace(/\\/g, '/')}`], { encoding: 'utf8', timeout: 45000, maxBuffer: 8 * 1024 * 1024 });
const output = result.stdout?.match(/<pre id="result">([^<]+)<\/pre>/)?.[1];
if (!output || output === 'PENDING') {
  console.error(result.error?.message || result.stderr); process.exitCode = 1;
} else {
  const results = JSON.parse(output.replace(/&quot;/g, '"').replace(/&amp;/g, '&'));
  results.forEach(item => console.log(`${item.ok ? 'PASS' : 'FAIL'}: ${item.width}px${item.error ? ' — ' + item.error : ''}`));
  console.log('Screenshot: ' + screenshot);
  process.exitCode = results.every(item => item.ok) ? 0 : 1;
}
