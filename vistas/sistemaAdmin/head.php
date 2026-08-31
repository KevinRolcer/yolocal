<?php
if (!defined("RUTA")) {
    $ylAdminConfig = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "config.php";
    if (is_file($ylAdminConfig)) {
        require_once $ylAdminConfig;
    }
}

if (!function_exists("ylAssetUrl")) {
    function ylAssetUrl($relativePath)
    {
        $relativePath = ltrim(str_replace("\\", "/", (string) $relativePath), "/");
        $basePath = defined("RUTA") ? rtrim((string) RUTA, "/") : "";
        $url = $basePath . "/" . $relativePath;
        $localPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace("/", DIRECTORY_SEPARATOR, $relativePath);

        if (is_file($localPath)) {
            $url .= "?v=" . filemtime($localPath);
        }

        return $url;
    }
}

$ylBasePublic = defined("RUTA") ? rtrim((string) RUTA, "/") : "";
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Yo local</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
<link href="<?= htmlspecialchars(ylAssetUrl("assets/img/LogoYolocal.png"), ENT_QUOTES, "UTF-8") ?>" rel="icon" />

<?php
$adminCssBase = [
	"assets/css/menu.css"
];

$adminCssFiles = isset($adminCssFiles) && is_array($adminCssFiles) ? $adminCssFiles : [];
$adminCssToLoad = array_values(array_unique(array_merge($adminCssBase, $adminCssFiles)));

foreach ($adminCssToLoad as $cssFile):
	$isSharedAdminCss = in_array($cssFile, $adminCssBase, true);
?>
<link
	rel="stylesheet"
	href="<?= htmlspecialchars(ylAssetUrl($cssFile), ENT_QUOTES, 'UTF-8') ?>"
	data-admin-css="<?= $isSharedAdminCss ? 'shared' : 'page' ?>"
>
<?php endforeach; ?>

<?php
$adminSession = (isset($_SESSION) && is_array($_SESSION)) ? $_SESSION : [];
$adminTopbarUser = [
	"id" => $adminSession["ID_Usuario"] ?? "0",
	"name" => $adminSession["nombre"] ?? "Usuario",
	"email" => $adminSession["correo"] ?? "",
	"role" => $adminSession["tipo"] ?? "admin",
	"avatar" => $adminSession["foto_perfil"] ?? ""
];
?>
<script>
window.adminTopbarUser = <?= json_encode($adminTopbarUser, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
window.YL_BASE_PATH = <?= json_encode($ylBasePublic, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
window.YL_controladorEventosUrl = function () {
	var suf = "/controladores/controladorEventos.php";
	if (typeof window.YL_BASE_PATH === "string") {
		var b = window.YL_BASE_PATH;
		return b === "" ? suf : b + suf;
	}
	try {
		var abs = new URL("../../controladores/controladorEventos.php", window.location.href);
		return abs.pathname || suf;
	} catch (err) {
		return suf;
	}
};
</script>
<script defer src="<?= htmlspecialchars(ylAssetUrl("assets/js/notificaciones.js"), ENT_QUOTES, "UTF-8") ?>"></script>
<script defer src="<?= htmlspecialchars(ylAssetUrl("assets/js/admin-navigation.js"), ENT_QUOTES, "UTF-8") ?>"></script>
