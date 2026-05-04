(function () {
    if (window.__adminTopbarInitialized) {
        return;
    }
    window.__adminTopbarInitialized = true;

    function escapeHtml(value) {
        return String(value || "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/\"/g, "&quot;")
            .replace(/'/g, "&#39;");
    }

    function getUserData() {
        var fromWindow = window.adminTopbarUser || {};
        return {
            id: String(fromWindow.id || "0"),
            name: String(fromWindow.name || "Usuario"),
            email: String(fromWindow.email || ""),
            role: String(fromWindow.role || "admin"),
            serverAvatar: String(fromWindow.avatar || ""),
            defaultAvatar: "assets/img/descarga.gif"
        };
    }

    function buildCalendarRows(date) {
        var weekdays = ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"];
        var days = [];
        for (var i = 0; i < 7; i += 1) {
            days.push(date.getDate() - date.getDay() + i);
        }
        return { weekdays: weekdays, days: days };
    }

    function buildTopbarMarkup(user, avatarSrc) {
        var now = new Date();
        var monthYear = now.toLocaleDateString("es-MX", { month: "short", year: "numeric" });
        var cal = buildCalendarRows(now);
        var dayHtml = cal.days
            .map(function (day) {
                var isToday = day === now.getDate();
                return '<div class="admin-day' + (isToday ? ' today' : '') + '">' + day + "</div>";
            })
            .join("");

        return '' +
            '<div class="admin-topbar-widget">' +
            '  <button type="button" class="admin-icon-btn" data-admin-notif-toggle aria-label="Abrir notificaciones">' +
            '    <i class="ri-notification-3-line admin-notif-icon"></i>' +
            '  </button>' +
            '  <span class="admin-badge-dot" aria-hidden="true"></span>' +
            '  <div class="admin-panel admin-notif-panel" data-admin-notif-panel>' +
            '    <div class="admin-panel-header">' +
            '      <h4 class="admin-panel-title">' + escapeHtml(monthYear) + '</h4>' +
            '      <div class="admin-week">' + cal.weekdays.map(function (w) { return "<span>" + w + "</span>"; }).join("") + '</div>' +
            '      <div class="admin-days">' + dayHtml + '</div>' +
            '    </div>' +
            '    <div class="admin-notif-list">' +
            '      <div class="admin-notif-item"><div><strong>Bienvenido a la nueva sección de notificaciones</strong></div><i class="ri-arrow-right-s-line admin-notif-chevron"></i></div>' +
            '    </div>' +
            '  </div>' +
            '</div>' +
            '<div class="admin-topbar-widget">' +
            '  <button type="button" class="admin-profile-trigger" data-admin-profile-toggle aria-label="Abrir menu de perfil">' +
            '    <span class="admin-avatar-wrap"><img src="' + escapeHtml(avatarSrc) + '" alt="Foto de perfil" data-admin-avatar-main></span>' +
            '    <span class="admin-profile-meta">' +
            '      <span class="admin-profile-name">' + escapeHtml(user.name) + '</span>' +
            '      <span class="admin-profile-role">' + escapeHtml(user.role) + '</span>' +
            '    </span>' +
            '    <i class="ri-arrow-down-s-line admin-arrow-icon"></i>' +
            '  </button>' +
            '  <div class="admin-panel admin-profile-panel" data-admin-profile-panel>' +
            '    <div class="admin-profile-card">' +
            '      <span class="admin-avatar-wrap"><img src="' + escapeHtml(avatarSrc) + '" alt="Foto de perfil" data-admin-avatar-panel></span>' +
            '      <div class="admin-profile-meta">' +
            '        <span class="admin-profile-name">' + escapeHtml(user.name) + '</span>' +
            '        <span class="admin-profile-role">' + escapeHtml(user.email || user.role) + '</span>' +
            '        <button type="button" class="admin-avatar-picker" data-admin-avatar-picker>' +
            '          <i class="bi bi-camera"></i>Cambiar foto' +
            '        </button>' +
            '      </div>' +
            '      <input type="file" accept="image/*" data-admin-avatar-input hidden>' +
            '    </div>' +
            '    <div class="admin-profile-menu">' +
            '      <button type="button" class="admin-profile-option" data-admin-open-profile>' +
            '        <i class="bi bi-person"></i>Mi perfil' +
            '      </button>' +
            '      <a href="salir.php" class="admin-profile-option signout">' +
            '        <i class="bi bi-box-arrow-right"></i>Salir' +
            '      </a>' +
            '    </div>' +
            '  </div>' +
            '</div>';
    }

    function setAvatarEverywhere(root, src) {
        var images = root.querySelectorAll("[data-admin-avatar-main], [data-admin-avatar-panel]");
        images.forEach(function (img) {
            img.src = src;
        });
    }

    function guardarAvatarLocal(storageKey, file, container) {
        var reader = new FileReader();
        reader.onload = function (loadEvent) {
            var result = String(loadEvent.target.result || "");
            setAvatarEverywhere(container, result);
            localStorage.setItem(storageKey, result);
        };
        reader.readAsDataURL(file);
    }

    function bindWidget(container, user) {
        var storageKey = "adminAvatar_" + user.id;
        var savedAvatar = localStorage.getItem(storageKey);
        var avatarSrc = savedAvatar || user.serverAvatar || user.defaultAvatar;

        container.innerHTML = buildTopbarMarkup(user, avatarSrc);

        var notifToggle = container.querySelector("[data-admin-notif-toggle]");
        var notifPanel = container.querySelector("[data-admin-notif-panel]");
        var profileToggle = container.querySelector("[data-admin-profile-toggle]");
        var profilePanel = container.querySelector("[data-admin-profile-panel]");
        var pickerBtn = container.querySelector("[data-admin-avatar-picker]");
        var pickerInput = container.querySelector("[data-admin-avatar-input]");
        var openProfileBtn = container.querySelector("[data-admin-open-profile]");

        function closePanels() {
            notifPanel.classList.remove("is-open");
            profilePanel.classList.remove("is-open");
            profileToggle.setAttribute("aria-expanded", "false");
        }

        profileToggle.setAttribute("aria-expanded", "false");

        notifToggle.addEventListener("click", function (event) {
            event.stopPropagation();
            var willOpen = !notifPanel.classList.contains("is-open");
            closePanels();
            if (willOpen) {
                notifPanel.classList.add("is-open");
            }
        });

        profileToggle.addEventListener("click", function (event) {
            event.stopPropagation();
            var willOpen = !profilePanel.classList.contains("is-open");
            closePanels();
            if (willOpen) {
                profilePanel.classList.add("is-open");
                profileToggle.setAttribute("aria-expanded", "true");
            }
        });

        pickerBtn.addEventListener("click", function (event) {
            event.preventDefault();
            pickerInput.click();
        });

        pickerInput.addEventListener("change", function () {
            var file = pickerInput.files && pickerInput.files[0];
            if (!file) {
                return;
            }
            if (!file.type.startsWith("image/")) {
                alert("Selecciona una imagen valida.");
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                alert("La imagen debe ser menor a 2MB.");
                return;
            }

            var formData = new FormData();
            formData.append("ope", "SUBIR_FOTO_PERFIL");
            formData.append("ID_Usuario", user.id);
            formData.append("fotoPerfil", file);

            fetch("controladores/controladorUsuarios.php", {
                method: "POST",
                body: formData
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error("No se pudo subir la imagen.");
                    }
                    return response.json();
                })
                .then(function (data) {
                    if (data && data.success && data.storage === "server" && data.ruta) {
                        var serverAvatar = String(data.ruta) + "?v=" + Date.now();
                        setAvatarEverywhere(container, serverAvatar);
                        localStorage.removeItem(storageKey);
                        window.adminTopbarUser = window.adminTopbarUser || {};
                        window.adminTopbarUser.avatar = String(data.ruta);
                        return;
                    }

                    guardarAvatarLocal(storageKey, file, container);
                })
                .catch(function () {
                    guardarAvatarLocal(storageKey, file, container);
                });
        });

        openProfileBtn.addEventListener("click", function () {
            var profileModalId = "adminProfileModal";
            var existingModal = document.getElementById(profileModalId);

            if (!existingModal) {
                var modalMarkup = '' +
                    '<div class="modal fade" id="' + profileModalId + '" tabindex="-1" aria-hidden="true">' +
                    '  <div class="modal-dialog modal-dialog-centered">' +
                    '    <div class="modal-content">' +
                    '      <div class="modal-header">' +
                    '        <h5 class="modal-title">Mi perfil</h5>' +
                    '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>' +
                    '      </div>' +
                    '      <div class="modal-body text-center">' +
                    '        <img src="' + escapeHtml((savedAvatar || user.serverAvatar || user.defaultAvatar)) + '" alt="Foto" style="width:90px;height:90px;border-radius:50%;object-fit:cover;">' +
                    '        <h5 style="margin-top:12px;">' + escapeHtml(user.name) + '</h5>' +
                    '        <p style="margin-bottom:6px;color:#6f6f6f;">' + escapeHtml(user.email || "Sin correo") + '</p>' +
                    '        <p style="margin:0;color:#6f6f6f;text-transform:capitalize;">Rol: ' + escapeHtml(user.role) + '</p>' +
                    '      </div>' +
                    '    </div>' +
                    '  </div>' +
                    '</div>';
                document.body.insertAdjacentHTML("beforeend", modalMarkup);
                existingModal = document.getElementById(profileModalId);
            }

            closePanels();
            var modal = window.bootstrap ? new bootstrap.Modal(existingModal) : null;
            if (modal) {
                var modalImg = existingModal.querySelector("img");
                if (modalImg) {
                    modalImg.src = container.querySelector("[data-admin-avatar-main]").src;
                }
                modal.show();
            }
        });

        document.addEventListener("click", function (event) {
            if (!container.contains(event.target)) {
                closePanels();
            }
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") {
                closePanels();
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        var user = getUserData();
        var containers = document.querySelectorAll(".topbar .contenedor");
        if (!containers.length) {
            return;
        }

        containers.forEach(function (container) {
            bindWidget(container, user);
        });
    });

    // Compatibility with legacy inline handler present in old HTML.
    window.toggleNotifi = function () {
        return false;
    };
})();
