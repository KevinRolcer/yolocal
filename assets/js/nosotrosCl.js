class YoLocalUI {
    constructor() {
        document.addEventListener("DOMContentLoaded", () => {
            this.initSmoothScroll();
            this.initFadeInObserver();
            this.initCardHover();
        });
    }

    initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener("click", (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute("href"));
                if (target) {
                    target.scrollIntoView({
                        behavior: "smooth",
                        block: "start"
                    });
                }
            });
        });
    }

    initFadeInObserver() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: "0px 0px -50px 0px"
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("visible");
                }
            });
        }, observerOptions);

        document.querySelectorAll(".fade-in").forEach(el => observer.observe(el));
    }

    initCardHover() {
        document.querySelectorAll(".logo-card, .valor-card").forEach(card => {
            card.addEventListener("mouseenter", () => {
                card.style.transform = "translateY(-10px) scale(1.02)";
            });
            card.addEventListener("mouseleave", () => {
                card.style.transform = "translateY(0) scale(1)";
            });
        });
    }
}

new YoLocalUI();
