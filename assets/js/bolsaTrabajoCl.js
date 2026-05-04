class JobPlatform {
    constructor() {
        this.jobs = [];
        this.filteredJobs = [];
        this.jobDetails = {};
        this.selectedJobId = null;
        this.init();
    }

    async init() {
        await this.fetchJobs();
        this.setupEventListeners();
        this.applyFilters();
        this.setupModalEvents();
    }

    async fetchJobs() {
        try {
            const response = await fetch("../controladores/controladorTrabajosPag.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ ope: "LISTAR_TRABAJOS" })
            });
            const data = await response.json();

            if (data.success) {
                this.jobs = Array.isArray(data.trabajos) ? data.trabajos : [];
                this.filteredJobs = [...this.jobs];
            } else {
                console.error("Error al traer trabajos:", data.msg);
            }
        } catch (error) {
            console.error("Error en fetchJobs:", error);
        }
    }

    normalize(value) {
        return String(value ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    escapeHtml(value) {
        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    getJobId(job) {
        return `job-${job.ID_Trabajo}`;
    }

    getSalaryNumber(job) {
        const value = Number(String(job.Salario ?? "0").replace(/[^\d.-]/g, ""));
        return Number.isFinite(value) ? value : 0;
    }

    normalizeAssetPath(path) {
        const cleanPath = String(path ?? "").trim();
        if (!cleanPath) return "";
        if (/^(https?:)?\/\//i.test(cleanPath) || cleanPath.startsWith("../") || cleanPath.startsWith("/")) {
            return cleanPath;
        }
        if (cleanPath.startsWith("assets/")) return `../${cleanPath}`;
        return cleanPath;
    }

    buildJobDetail(job) {
        const company = job.nombre_negocio || "Negocio local";
        return {
            id: this.getJobId(job),
            title: job.Titulo || "Vacante disponible",
            company,
            logo: company.charAt(0).toUpperCase(),
            logoPath: this.normalizeAssetPath(job.Rutaicono),
            logoColor: "linear-gradient(45deg, #613f9b, #7c5cff)",
            description: job.Descripcion || "Sin descripción disponible.",
            tipoHorario: job.Tipo_Horario || "",
            salario: job.Salario,
            personasRequeridas: job.PerRequeridas,
            correo: job.CorreoN,
            telefono: job.Telefono,
            direccion: job.Direccion
        };
    }

    applyFilters() {
        const searchTerm = this.normalize(document.getElementById("searchInput")?.value || "");
        const schedule = document.getElementById("filterSchedule")?.value || "";
        const salary = document.getElementById("filterSalary")?.value || "";
        const sortValue = document.getElementById("sortJobs")?.value || "recent";

        this.filteredJobs = this.jobs.filter((job) => {
            const haystack = this.normalize([
                job.Titulo,
                job.Descripcion,
                job.nombre_negocio,
                job.Direccion
            ].join(" "));
            const jobSchedule = String(job.Tipo_Horario ?? "").trim();
            const salaryNumber = this.getSalaryNumber(job);

            const matchesSearch = !searchTerm || haystack.includes(searchTerm);
            const matchesSchedule = !schedule ||
                (schedule === "__SIN_TURNO__"
                    ? !jobSchedule
                    : this.normalize(jobSchedule) === this.normalize(schedule));
            const matchesSalary = !salary ||
                (salary === "paid" ? salaryNumber > 0 : salaryNumber <= 0);

            return matchesSearch && matchesSchedule && matchesSalary;
        });

        this.sortJobs(sortValue);
        this.renderJobs(this.filteredJobs);
    }

    sortJobs(sortValue) {
        const compareTitle = (a, b) => String(a.Titulo ?? "").localeCompare(String(b.Titulo ?? ""), "es");
        const compareSalary = (a, b) => this.getSalaryNumber(a) - this.getSalaryNumber(b);

        if (sortValue === "title") this.filteredJobs.sort(compareTitle);
        if (sortValue === "salary-desc") this.filteredJobs.sort((a, b) => compareSalary(b, a));
        if (sortValue === "salary-asc") this.filteredJobs.sort(compareSalary);
        if (sortValue === "recent") this.filteredJobs.sort((a, b) => Number(b.ID_Trabajo) - Number(a.ID_Trabajo));
    }

    renderJobs(trabajos) {
        const jobsList = document.querySelector(".jobs-list");
        if (!jobsList) return;

        const header = jobsList.querySelector(".jobs-header");
        jobsList.innerHTML = "";
        if (header) jobsList.appendChild(header);

        this.jobDetails = {};

        if (!trabajos.length) {
            const emptyState = document.createElement("div");
            emptyState.className = "jobs-empty-state";
            emptyState.innerHTML = `
                <strong>No encontramos vacantes</strong>
                <span>Prueba con otros filtros o una búsqueda más amplia.</span>
            `;
            jobsList.appendChild(emptyState);
            this.updateJobCount();
            this.clearJobDetails();
            return;
        }

        trabajos.forEach((job) => {
            const detail = this.buildJobDetail(job);
            this.jobDetails[detail.id] = detail;

            const scheduleText = detail.tipoHorario || "Sin turno";
            const salaryText = this.getSalaryNumber(job) > 0 ? `$${this.escapeHtml(job.Salario)}` : "Salario no especificado";
            const logo = detail.logoPath
                ? `<img src="${this.escapeHtml(detail.logoPath)}" alt="Logo de ${this.escapeHtml(detail.company)}">`
                : this.escapeHtml(detail.logo);

            const jobItem = document.createElement("article");
            jobItem.classList.add("job-item");
            jobItem.dataset.jobId = detail.id;
            jobItem.innerHTML = `
                <div class="job-header">
                    <div class="company-logo" style="background: ${detail.logoColor};">${logo}</div>
                    <div class="job-info">
                        <div class="job-title-row">
                            <h3>${this.escapeHtml(detail.title)}</h3>
                            <span class="job-salary-chip">${salaryText}</span>
                        </div>
                        <div class="company-name">${this.escapeHtml(detail.company)}</div>
                        <p class="job-description">${this.escapeHtml(detail.description)}</p>
                        <div class="job-location">${this.escapeHtml(detail.direccion || "Ubicación no especificada")}</div>
                    </div>
                </div>
                <div class="job-meta">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Z" />
                        </svg>
                        ${this.escapeHtml(detail.personasRequeridas || "1")} vacante${Number(detail.personasRequeridas) === 1 ? "" : "s"}
                    </span>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        ${this.escapeHtml(scheduleText)}
                    </span>
                </div>
            `;
            jobsList.appendChild(jobItem);
        });

        const selectedStillVisible = this.selectedJobId && this.jobDetails[this.selectedJobId];
        const nextSelectedId = selectedStillVisible ? this.selectedJobId : this.getJobId(trabajos[0]);
        const nextElement = jobsList.querySelector(`[data-job-id="${nextSelectedId}"]`);
        if (nextElement) this.selectJob(nextElement, nextSelectedId, false);

        this.updateJobCount();
    }

    setupEventListeners() {
        document.addEventListener("click", (e) => {
            const jobItem = e.target.closest(".job-item");
            if (jobItem?.dataset.jobId) this.selectJob(jobItem, jobItem.dataset.jobId);
        });

        ["searchInput", "filterSchedule", "filterSalary", "sortJobs"].forEach((id) => {
            document.getElementById(id)?.addEventListener(id === "searchInput" ? "input" : "change", () => this.applyFilters());
        });

        document.getElementById("clearJobFilters")?.addEventListener("click", () => {
            const search = document.getElementById("searchInput");
            const schedule = document.getElementById("filterSchedule");
            const salary = document.getElementById("filterSalary");
            const sort = document.getElementById("sortJobs");
            if (search) search.value = "";
            if (schedule) schedule.value = "";
            if (salary) salary.value = "";
            if (sort) sort.value = "recent";
            this.applyFilters();
        });
    }

    setupModalEvents() {
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") this.closeJobModal();
        });

        const jobModal = document.getElementById("jobModal");
        if (jobModal) {
            jobModal.addEventListener("click", (e) => {
                if (e.target === jobModal) this.closeJobModal();
            });
        }
    }

    selectJob(element, jobId, openOnMobile = true) {
        document.querySelectorAll(".job-item").forEach((item) => item.classList.remove("active"));
        element.classList.add("active");
        this.selectedJobId = jobId;
        this.updateJobDetails(jobId);

        if (openOnMobile && window.innerWidth <= 768) this.openJobModal();
    }

    renderLogo(target, job) {
        if (!target || !job) return;
        target.style.background = job.logoColor;
        target.innerHTML = job.logoPath
            ? `<img src="${this.escapeHtml(job.logoPath)}" alt="Logo de ${this.escapeHtml(job.company)}">`
            : this.escapeHtml(job.logo);
    }

    updateJobDetails(jobId) {
        const job = this.jobDetails[jobId];
        if (!job) return;

        const salaryText = this.getSalaryNumber({ Salario: job.salario }) > 0 ? `$${job.salario}` : "No especificado";
        const scheduleText = job.tipoHorario || "No especificado";
        const phoneText = job.telefono || job.correo || "Contacto no disponible";

        [
            { root: ".job-details", content: ".job-details-content" },
            { root: "#jobModal", content: "#modalJobContent" }
        ].forEach((scope) => {
            const root = document.querySelector(scope.root);
            if (!root) return;

            const title = root.querySelector("h2");
            const company = root.querySelector(".company");
            const logo = root.querySelector(".detail-company-logo");
            const description = root.querySelector(".detail-section p");
            const salary = root.querySelector(".detail-value");
            const schedule = root.querySelector(".detail-Horario");
            const phone = root.querySelector(".number-section a, .detail-section a");
            const apply = root.querySelector(".apply-btn");

            if (title) title.textContent = job.title;
            if (company) company.textContent = job.company;
            this.renderLogo(logo, job);
            if (description) description.textContent = job.description;
            if (salary) salary.textContent = `Salario: ${salaryText}`;
            if (schedule) schedule.textContent = `Tipo de horario: ${scheduleText}`;
            if (phone) {
                phone.textContent = phoneText;
                phone.href = job.telefono ? `tel:${job.telefono}` : (job.correo ? `mailto:${job.correo}` : "#");
            }
            if (apply) {
                apply.onclick = () => {
                    if (job.telefono) window.location.href = `tel:${job.telefono}`;
                    else if (job.correo) window.location.href = `mailto:${job.correo}`;
                };
            }
        });
    }

    clearJobDetails() {
        const title = document.querySelector(".job-details h2");
        const company = document.querySelector(".job-details .company");
        const description = document.querySelector(".job-details .detail-section p");
        if (title) title.textContent = "Sin vacantes";
        if (company) company.textContent = "Ajusta tus filtros";
        if (description) description.textContent = "No hay empleos que coincidan con la búsqueda actual.";
    }

    openJobModal() {
        const modal = document.getElementById("jobModal");
        if (modal) {
            modal.classList.add("active");
            document.body.style.overflow = "hidden";
        }
    }

    closeJobModal() {
        const modal = document.getElementById("jobModal");
        if (modal) {
            modal.classList.remove("active");
            document.body.style.overflow = "";
        }
    }

    updateJobCount() {
        const jobsCount = document.querySelector(".jobs-count");
        const total = this.filteredJobs.length;
        if (jobsCount) jobsCount.textContent = `${total} empleo${total !== 1 ? "s" : ""} activo${total !== 1 ? "s" : ""}`;
    }
}

function closeJobModal() {
    if (window.jobPlatformInstance) window.jobPlatformInstance.closeJobModal();
}

function toggleFilters() {
    document.getElementById("jobFiltersPanel")?.classList.toggle("active");
}

document.addEventListener("DOMContentLoaded", () => {
    window.jobPlatformInstance = new JobPlatform();
});
