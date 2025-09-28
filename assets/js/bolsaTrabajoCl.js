class JobPlatform {
    constructor() {
        this.jobDetails = {}; 
        this.init();
    }

    async init() {
        await this.fetchJobs(); 
        this.setupEventListeners();
        this.setupSearch();
        this.setupModalEvents();
    }

    async fetchJobs() {
        try {
            const response = await fetch("../../controladores/controladorTrabajosPag.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ ope: "LISTAR_TRABAJOS" })
            });
            const data = await response.json();

            if (data.success) {
                this.renderJobs(data.trabajos);
            } else {
                console.error("Error al traer trabajos:", data.msg);
            }
        } catch (error) {
            console.error("Error en fetchJobs:", error);
        }
    }

renderJobs(trabajos) {
    const jobsList = document.querySelector(".jobs-list");
    if (!jobsList) return;

    const header = jobsList.querySelector(".jobs-header");
    jobsList.innerHTML = "";
    jobsList.appendChild(header);


    trabajos.forEach((job) => {
        const jobId = `job-${job.ID_Trabajo}`;

        this.jobDetails[jobId] = {
            title: job.Titulo,
            company: job.nombre_negocio,
            logo: job.Rutaicono ? "" : job.nombre_negocio.charAt(0),
            logoPath: job.Rutaicono,
            logoColor: "linear-gradient(45deg, #6366f1, #8b5cf6)",
            description: job.Descripcion,
            correo: job.CorreoN,
            telefono: job.Telefono,
            direccion: job.Direccion
        };

        const jobItem = document.createElement("div");
        jobItem.classList.add("job-item");
        jobItem.setAttribute("onclick", `selectJob(this, '${jobId}')`);
        jobItem.innerHTML = `
            <div class="job-header">
                <div class="company-logo" style="background: ${this.jobDetails[jobId].logoColor};">
                    ${job.Rutaicono 
                        ? `<img src="${job.Rutaicono}" alt="logo" style="width:100%;height:100%;border-radius:50%;">` 
                        : this.jobDetails[jobId].logo}
                </div>
                <div class="job-info">
                    <h3>${job.Titulo}</h3>
                    <div class="company-name">${job.nombre_negocio}</div>
                    <div class="job-description">${job.Direccion}</div>
                </div>
            </div>
            <div class="job-meta">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                    </svg>
                    ${job.CorreoN}
                </span>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    Personas requeridas
                </span>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Tipo de horario
                </span>
            </div>
        `;
        jobsList.appendChild(jobItem);
    });

    const firstJob = document.querySelector(".job-item");
    if (firstJob) {
        this.selectJob(firstJob, firstJob.getAttribute("onclick").match(/'([^']+)'/)[1]);
    }

    this.updateJobCount();
}


    setupEventListeners() {
        document.addEventListener("click", (e) => {
            if (e.target.closest(".job-item")) {
                const jobItem = e.target.closest(".job-item");
                const onclickAttr = jobItem.getAttribute("onclick");
                let jobId = null;

                if (onclickAttr) {
                    const match = onclickAttr.match(/selectJob\(this,\s*['"]([^'"]+)['"]\)/);
                    if (match && match[1]) {
                        jobId = match[1];
                    }
                }

                if (jobId) this.selectJob(jobItem, jobId);
            }
        });
    }

    setupSearch() {
        const searchInput = document.getElementById("searchInput");
        if (searchInput) {
            searchInput.addEventListener("input", (e) => {
                this.handleSearch(e.target.value);
            });
        }
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

    selectJob(element, jobId) {
        document.querySelectorAll(".job-item").forEach((item) => {
            item.classList.remove("active");
        });

        element.classList.add("active");
        this.updateJobDetails(jobId);

        if (window.innerWidth <= 768) {
            this.openJobModal();
        }
    }

    updateJobDetails(jobId) {
        const job = this.jobDetails[jobId];
        if (!job) return;

        const desktopTitle = document.querySelector(".job-details h2");
        const desktopCompany = document.querySelector(".job-details .company");
        const desktopLogo = document.querySelector(".job-details .detail-company-logo");
        const desktopDescription = document.querySelector(".job-details .detail-section p");
        const desktopPhone = document.querySelector(".job-details .number-section a");

        if (desktopTitle) desktopTitle.textContent = job.title;
        if (desktopCompany) desktopCompany.textContent = job.company;
        if (desktopLogo) {
            if (job.logoPath) {
                desktopLogo.innerHTML = `<img src="${job.logoPath}" alt="logo" style="width:100%;height:100%;border-radius:50%;">`;
            } else {
                desktopLogo.textContent = job.logo;
            }
            desktopLogo.style.background = job.logoColor;
        }
        if (desktopDescription) desktopDescription.textContent = job.description;
        if (desktopPhone) desktopPhone.textContent = job.telefono;

        const modalTitle = document.querySelector("#modalJobHeader h2");
        const modalCompany = document.querySelector("#modalJobHeader .company");
        const modalLogo = document.querySelector("#modalJobHeader .detail-company-logo");
        const modalDescription = document.querySelector("#modalJobContent .detail-section p");

        if (modalTitle) modalTitle.textContent = job.title;
        if (modalCompany) modalCompany.textContent = job.company;
        if (modalLogo) {
            if (job.logoPath) {
                modalLogo.innerHTML = `<img src="${job.logoPath}" alt="logo" style="width:100%;height:100%;border-radius:50%;">`;
            } else {
                modalLogo.textContent = job.logo;
            }
            modalLogo.style.background = job.logoColor;
        }
        if (modalDescription) modalDescription.textContent = job.description;
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
            document.body.style.overflow = "auto";
        }
    }

    handleSearch(searchTerm) {
        const jobItems = document.querySelectorAll(".job-item");
        const normalizedSearch = searchTerm.toLowerCase();

        jobItems.forEach((item) => {
            const title = item.querySelector("h3")?.textContent.toLowerCase() || "";
            const description = item.querySelector(".job-description")?.textContent.toLowerCase() || "";
            const companyName = item.querySelector(".company-name")?.textContent.toLowerCase() || "";

            const isVisible =
                title.includes(normalizedSearch) ||
                description.includes(normalizedSearch) ||
                companyName.includes(normalizedSearch);

            item.style.display = isVisible ? "block" : "none";
        });

        this.updateJobCount();
    }

    updateJobCount() {
        const visibleJobs = document.querySelectorAll('.job-item:not([style*="display: none"])').length;
        const jobsCount = document.querySelector(".jobs-count");
        if (jobsCount) {
            jobsCount.textContent = `${visibleJobs} empleo${visibleJobs !== 1 ? "s" : ""} activo${visibleJobs !== 1 ? "s" : ""}`;
        }
    }
}

function selectJob(element, jobId) {
    if (window.jobPlatformInstance) window.jobPlatformInstance.selectJob(element, jobId);
}

function closeJobModal() {
    if (window.jobPlatformInstance) window.jobPlatformInstance.closeJobModal();
}

document.addEventListener("DOMContentLoaded", () => {
    window.jobPlatformInstance = new JobPlatform();
});
