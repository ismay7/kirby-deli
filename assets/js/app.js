const everyThing = document.querySelector("html");
const body = document.querySelector("body");
const navMenu = document.querySelector("#nav-menu");
const navMenuList = document.querySelector("#nav-menu ul");
const navMenuListItems = document.querySelectorAll("#nav-menu ul li");
const navMenuListAnchors = document.querySelectorAll("#nav-menu ul li a");
const menuBtn = document.querySelector("#menu-btn");
const navIcon = document.querySelector("#nav-icon");
const shade = document.querySelector("#shade");
const dropdownLink = document.querySelectorAll(".dropdown-link");

/////////////////////////////////

// MENU Behaviour
menuBtn.addEventListener("click", () => {
	toggleSidebar();
});

function toggleSidebar() {
	navMenu.classList.toggle("open-navmenu");
    navMenuList.classList.toggle("visible");
    navIcon.classList.toggle("open");
	body.classList.toggle("noscroll");
}

function openSidebar() {
	navMenu.classList.add("open-navmenu");
    navMenuList.classList.add("visible");
    navIcon.classList.add("open");
    body.classList.add("noscroll");
}

function closeSidebar() {
	navMenu.classList.remove("open-navmenu");
    navMenuList.classList.remove("visible");
    navIcon.classList.remove("open");
    body.classList.remove("noscroll");
}

// close sidebar if anchor link is clicked on mobile
navMenuListAnchors.forEach(anchor => {
	anchor.addEventListener("click", () => {
		toggleSidebar();
	});
});

// close sidebar if clicked onto other element than sidebar
everyThing.addEventListener("mouseup", function (event) {
	if (
		!navMenu.contains(event.target) &&
		event.target.id !== "nav-icon" &&
		event.target.id !== "menu-icon" &&
		!event.target.classList.contains("nav-span")
	) {
		closeSidebar();
	}
});

// open and close dropdown of submenus
dropdownLink.forEach(function (dropdown) {
	dropdown.addEventListener("click", function (event) {
		clickedSubmenuLink = event.target.closest("a");
		submenu = clickedSubmenuLink.nextElementSibling;
		chevron = clickedSubmenuLink.firstElementChild.nextElementSibling;
		chevron.classList.toggle("rotate");

		// slide down
		if (!submenu.classList.contains("visible")) {
			// show the submenu
			submenu.classList.add("visible");
			submenu.style.height = "auto";
			clickedSubmenuLink.classList.add("active");

			// Get the computed height of the submenu
			const heightSubmenu = submenu.clientHeight + "px";

			// Set the height of the content as 0px
			// so we can trigger the slide down animation
			submenu.style.height = "0px";

			// Do this after 0px has applied
			setTimeout(() => {
				submenu.style.height = heightSubmenu;
			}, 0);

			// slide up
		} else {
			// Set the height as 0px to trigger the slide up animation
			submenu.style.height = "0px";
			clickedSubmenuLink.classList.remove("active");

			// Remove the "visible" class when the transition ends
			submenu.addEventListener(
				"transitionend",
				() => {
					submenu.classList.remove("visible");
				},
				{ once: true }
			);
		}
	});
});
/////////////////////////////

// Logo Resizing/Vanishing on scrolling from the top on home 
if (document.body.classList.contains('is-home')) {

  const logo = document.querySelector("#logo");
  const marquee = document.querySelector("#marquee");
  const navMenu = document.querySelector("#nav-menu");

  // safety guard
  if (logo && navMenu) {
    window.addEventListener('scroll', () => {
      if (window.scrollY >= 200) {
        navMenu.style.backgroundColor = "var(--blue)";
        navMenu.style.boxShadow = "var(--boxShadow)";
        logo.style.opacity = "1";

        if (marquee) {
          marquee.style.backgroundColor = "var(--blue-light)";
          // marquee.style.backdropFilter = "blur(8px)";
        }

      } else {
        navMenu.style.backgroundColor = "transparent";
        navMenu.style.boxShadow = "none";
        logo.style.opacity = "0";

        if (marquee) {
          marquee.style.backgroundColor = "rgba(0,0,0,0.5)";
          marquee.style.backdropFilter = "blur(8px)";
        }
      }
    });
  }
}

const toggle = document.querySelector('.menu-toggle');

  menuBtn.addEventListener('click', () => {
    const isOpen = navMenu.classList.toggle('is-open');
    menuBtn.classList.toggle('is-open', isOpen);
    menuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  });

///////////////////////////

// Image Gallery
document.addEventListener('DOMContentLoaded', () => {
  const slider = document.querySelector('[data-slider="imgslider"]');
  if (!slider) return;

  const slides = Array.from(slider.querySelectorAll('.img-slide'));
  const prev   = slider.querySelector('.img-slider-prev');
  const next   = slider.querySelector('.img-slider-next');
  const dots   = Array.from(slider.querySelectorAll('.img-slider-dot'));

  if (slides.length <= 1) {
    prev?.remove();
    next?.remove();
    slider.querySelector('.img-slider-dots')?.remove();
    return;
  }

  // initial index: falls im HTML schon is-active gesetzt ist
  let index = slides.findIndex(slide => slide.classList.contains('is-active'));
  if (index < 0) index = 0;

  // sicherstellen, dass nur der korrekte Slide/Dot aktiv ist
  function applyActive() {
    slides.forEach((slide, i) => {
      slide.classList.toggle('is-active', i === index);
    });
    dots.forEach((dot, i) => {
      dot.classList.toggle('is-active', i === index);
    });
  }
  applyActive();

  function show(i) {
    index = (i + slides.length) % slides.length;
    applyActive();
  }

  prev?.addEventListener('click', () => show(index - 1));
  next?.addEventListener('click', () => show(index + 1));

  dots.forEach((dot, i) => {
    dot.addEventListener('click', () => show(i));
  });

  // Auto-Play
  function nextSlide() {
    show(index + 1);
  }

  let autoplay = setInterval(nextSlide, 2000); // 5 Sekunden

});

///////////////////

document.addEventListener("DOMContentLoaded", () => {
  // AOS (only if present)
  if (window.AOS) AOS.init();

  // Typed (only on home + only if target exists + lib loaded)
  const typerEl = document.querySelector("#typer");
  if (typerEl && window.Typed) {
    new Typed("#typer", {
      strings: ['la vita', 'la pizza', 'la pasta', "l'amore"],
      typeSpeed: 75,
      loop: true,
      showCursor: false,
    });
  }

});
