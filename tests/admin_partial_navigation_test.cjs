const fs = require('node:fs');
const path = require('node:path');
const os = require('node:os');
const http = require('node:http');
const { spawn } = require('node:child_process');

const root = path.resolve(__dirname, '..');
const home = fs.readFileSync(path.join(root, 'vistas/sistemaAdmin/home.php'), 'utf8');
const navigation = fs.readFileSync(path.join(root, 'assets/js/admin-navigation.js'), 'utf8');
const counterMarkup = [...home.matchAll(/<div class="stat-number"[^>]*>[\s\S]*?<\/div>/g)].map(match => match[0]);
const homeScripts = [...home.matchAll(/<script>([\s\S]*?)<\/script>/g)].map(match => match[0]).join('');
const requests = { home: 0 };

function shell(content, styles = '') {
  return `<!doctype html><html><head>${styles}</head><body><div class="navigation admin-sidebar"><ul class="main-menu">
    <li><a id="home-link" href="/page?pag=home">Inicio</a></li>
    <li><a id="section-link" href="/page?pag=section">Sección</a></li>
    <li><a id="slow-link" href="/page?pag=slow">Lenta</a></li>
    <li><a id="late-link" href="/page?pag=late">Atrasada</a></li>
    </ul></div><div class="main"><div class="topbar">Barra</div>${content}</div></body></html>`;
}

const browserTests = `
  const results = [];
  const pause = ms => new Promise(resolve => setTimeout(resolve, ms));
  const check = (value, message) => { if (!value) throw new Error(message); };
  async function until(predicate) {
    for (let i = 0; i < 100; i++) { if (predicate()) return; await pause(20); }
    throw new Error('La navegación no terminó');
  }
  async function run(name, test) {
    try { await test(); results.push({ name, ok: true }); }
    catch(error) { results.push({ name, ok: false, error: error.message }); }
  }
  addEventListener('load', async () => {
    await run('Conserva contenido y CSS mientras carga el siguiente módulo', async () => {
      const pendingStyles = new Promise((resolve, reject) => {
        const observer = new MutationObserver(() => {
          if (!document.querySelector('link[href$="slow.css"]')) return;
          observer.disconnect();
          try {
            check(document.getElementById('content').textContent === 'Inicial', 'Reemplazó el contenido antes de cargar el CSS');
            check(getComputedStyle(document.getElementById('content')).color === 'rgb(180, 0, 0)', 'Retiró los estilos antes de tiempo');
            resolve();
          } catch (error) { reject(error); }
        });
        observer.observe(document.head, { childList: true });
      });
      document.getElementById('slow-link').click();
      await pendingStyles;
    });
    await until(() => document.getElementById('content')?.textContent === 'Lenta');
    await pause(700);
    await run('Aplica los estilos del nuevo módulo', async () => {
      check(getComputedStyle(document.getElementById('content')).color === 'rgb(0, 120, 0)', 'No aplicó el CSS nuevo');
    });
    await run('Mantiene el orden de cascada al reutilizar CSS', async () => {
      document.getElementById('section-link').click();
      await until(() => document.getElementById('content')?.textContent === 'Sección');
      check(getComputedStyle(document.getElementById('content')).color === 'rgb(0, 0, 180)', 'El CSS compartido quedó en orden incorrecto');
    });
    await run('Actualiza los contadores en visitas repetidas a Inicio', async () => {
      for (let visit = 1; visit <= 2; visit++) {
        document.getElementById('home-link').click();
        await until(() => document.querySelector('.stat-number'));
        const counters = [...document.querySelectorAll('.stat-number')];
        check(counters.length === 3 && counters.every((counter, index) => counter.textContent === String(10 + visit + index)), 'Los contadores deben mostrar los totales nuevos al entrar a Inicio');
        document.getElementById('section-link').click();
        await until(() => document.getElementById('content')?.textContent === 'Sección');
      }
    });
    await run('Los scripts clásicos conservan una URL estable', async () => {
      const script = document.querySelector('script[src*="library.js"]');
      check(script && !new URL(script.src).searchParams.has('pjax'), 'Se invalida la caché del script clásico');
    });
    await run('Una navegación atrasada no reemplaza la sección más reciente', async () => {
      const observer = new MutationObserver(() => {
        if (!document.querySelector('link[href$="late.css"]')) return;
        observer.disconnect();
        document.getElementById('section-link').click();
      });
      observer.observe(document.head, { childList: true });
      document.getElementById('late-link').click();
      await pause(1200);
      check(document.getElementById('content')?.textContent === 'Sección', 'La respuesta atrasada sustituyó la última sección');
      check(!document.querySelector('link[href$="late.css"]'), 'Quedaron estilos de una navegación descartada');
    });
    document.getElementById('result').textContent = JSON.stringify(results);
  });
`;

const server = http.createServer((req, res) => {
  const url = new URL(req.url, 'http://localhost');
  if (url.pathname === '/navigation.js') {
    res.setHeader('Content-Type', 'text/javascript'); return res.end(navigation);
  }
  if (url.pathname === '/library.js') {
    res.setHeader('Content-Type', 'text/javascript'); return res.end('window.libraryLoaded = true;');
  }
  if (url.pathname.endsWith('.css')) {
    res.setHeader('Content-Type', 'text/css');
    const color = url.pathname === '/slow.css' ? '0,120,0' : url.pathname === '/base.css' ? '0,0,180' : '180,0,0';
    return setTimeout(() => res.end('#content { color: rgb(' + color + '); }'), ['/slow.css', '/late.css'].includes(url.pathname) ? 500 : 0);
  }
  res.setHeader('Content-Type', 'text/html; charset=utf-8');
  if (url.pathname === '/page') {
    if (url.searchParams.get('pag') === 'home') {
      requests.home++;
      const counters = counterMarkup.map((markup, index) => markup.replace(/<\?=[\s\S]*?\?>/g, String(10 + requests.home + index))).join('');
      return res.end(shell(counters + homeScripts));
    }
    if (url.searchParams.get('pag') === 'slow') {
      return res.end(shell('<div id="content">Lenta</div>', '<link rel="stylesheet" data-admin-css="page" href="/base.css"><link rel="stylesheet" data-admin-css="page" href="/slow.css">'));
    }
    if (url.searchParams.get('pag') === 'late') {
      return res.end(shell('<div id="content">Atrasada</div>', '<link rel="stylesheet" data-admin-css="page" href="/late.css">'));
    }
    return res.end(shell('<div id="content">Sección</div><script src="/library.js"></script>', '<link rel="stylesheet" data-admin-css="page" href="/slow.css"><link rel="stylesheet" data-admin-css="page" href="/base.css">'));
  }
  res.end(shell('<div id="content">Inicial</div><pre id="result">PENDING</pre>', '<link rel="stylesheet" data-admin-css="page" href="/initial.css"><script defer src="/navigation.js"></script>')
    .replace('</body>', `<script>${browserTests}</script></body>`)
    .replace('<pre id="result">PENDING</pre>', '')
    .replace('</body>', '<pre id="result">PENDING</pre></body>'));
});

server.listen(0, '127.0.0.1', () => {
  const profile = fs.mkdtempSync(path.join(os.tmpdir(), 'yolocal-partial-'));
  const browser = spawn(process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe', [
    '--headless=new', '--disable-gpu', '--no-first-run', '--no-default-browser-check', `--user-data-dir=${profile}`,
    '--dump-dom', '--virtual-time-budget=15000', `http://127.0.0.1:${server.address().port}/`
  ]);
  let output = '';
  let errors = '';
  browser.stdout.on('data', data => { output += data; });
  browser.stderr.on('data', data => { errors += data; });
  const timeout = setTimeout(() => browser.kill(), 45000);
  browser.on('close', () => {
    clearTimeout(timeout);
    server.close();
    const result = output.match(/<pre id="result">([^<]+)<\/pre>/)?.[1];
    if (!result || result === 'PENDING') {
      console.error('FAIL: el navegador no terminó las pruebas.\n' + errors);
      process.exitCode = 1; return;
    }
    const results = JSON.parse(result.replace(/&quot;/g, '"').replace(/&amp;/g, '&'));
    for (const item of results) console.log(`${item.ok ? 'PASS' : 'FAIL'}: ${item.name}${item.error ? ' — ' + item.error : ''}`);
    process.exitCode = results.every(item => item.ok) ? 0 : 1;
  });
});
