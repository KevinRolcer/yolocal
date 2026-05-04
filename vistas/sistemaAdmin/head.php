<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Yo local</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
<link href="assets/img/LogoYolocal.png" rel="icon" />

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
	href="<?= htmlspecialchars($cssFile, ENT_QUOTES, 'UTF-8') ?>"
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
</script>
<script defer src="assets/js/notificaciones.js"></script>
<script defer src="assets/js/admin-navigation.js"></script>
