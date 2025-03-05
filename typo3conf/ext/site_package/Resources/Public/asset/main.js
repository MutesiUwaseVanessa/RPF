document.addEventListener("DOMContentLoaded", function () {
  // get page url or pathname
  // let getPageUrl = "";

  // function getUrlWithoutDomain() {
  //   getPageUrl = window.location.pathname;

  //   return window.location.pathname;
  // }

  // getUrlWithoutDomain();

  // const getPageNameSet = getPageUrl.split("/");
  // let pageName = getPageNameSet[1];
  // //console.log(pageName);

  const getPageTitle = document.title;
  //console.log(getPageTitle);

  // const currentPath = window.location.pathname;
  // console.log(currentPath);
  // // Select all nav links
  // const navLinks = document.querySelectorAll(".nav-link");

  // navLinks.forEach((link) => {
  //   if (link.href.includes(currentPath)) {
  //     link.classList.add("active");
  //     console.log(link);
  //   }
  // });

  // header fetcher on all pages
  fetch("/header-breadcramp/header.html")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response of header was not ok");
      }
      return response.text();
    })
    .then((data) => {
      document.getElementById("header").innerHTML = data;
      document.querySelector(".title-holder .page-title").innerHTML =
        getPageTitle;
    })
    .catch((err) => {
      console.log("There was a problem with the fetch operation:", err);
    });

  // Footer fetcher on all pages
  fetch("/footer/index.html")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response of footer was not okay");
      }
      return response.text();
    })
    .then((data) => {
      document.getElementById("footer").innerHTML = data;
    })
    .catch((err) => {
      console.error("There was a problem with the fetch operation:", err);
    });

  // custom slider
  let currentSlide = 0;

  function showSlide(index) {
    const slides = document.querySelectorAll(".slides");
    if (index >= slides.length) currentSlide = 0;
    if (index < 0) currentSlide = slides.length - 1;

    slides.forEach((slide, i) => {
      slide.classList.remove("active");
      if (i === currentSlide) {
        slide.classList.add("active");
      }
    });
  }

  function changeSlide(step) {
    currentSlide += step;
    showSlide(currentSlide);
  }

  // Optional: Auto slideshow
  setInterval(() => {
    changeSlide(1);
  }, 5500);
});
