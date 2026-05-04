const toggle = document.querySelector(".toggle");
const navigation = document.querySelector(".navigation");
const main = document.querySelector(".main");

if (toggle && navigation && main) {
  toggle.addEventListener("click", () => {
    navigation.classList.toggle("active");
    main.classList.toggle("active");
  });
}
