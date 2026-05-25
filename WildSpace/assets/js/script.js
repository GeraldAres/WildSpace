// ============ SMOOTH NAVIGATION LINKS ============
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// ============ BUTTON INTERACTIONS ============
document.querySelectorAll('.cta-button').forEach(button => {
    button.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.style.position = 'absolute';
        ripple.style.borderRadius = '50%';
        ripple.style.background = 'rgba(255, 255, 255, 0.6)';
        ripple.style.pointerEvents = 'none';
        ripple.style.animation = 'rippleEffect 0.6s ease-out';

        this.style.position = 'relative';
        this.style.overflow = 'hidden';
        this.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// ============ NAV SCROLL EFFECT ============
let lastScrollTop = 0;
const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', () => {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > 100) {
        navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
    } else {
        navbar.style.boxShadow = '0 0 0 rgba(0, 0, 0, 0)';
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
});

// ============ ACTIVE NAV HIGHLIGHTING ============
const navLinks = document.querySelectorAll('.nav-link');

navLinks.forEach(link => {
    link.addEventListener('click', function(e) {
        navLinks.forEach(l => l.style.color = 'var(--text-dark)');
        this.style.color = 'var(--primary-black)';
    });
});

// ============ IMAGE HOVER EFFECT ============
const heroImage = document.querySelector('.hero-image');

if (heroImage) {
    heroImage.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.02)';
        this.style.transition = 'transform 0.3s ease';
    });

    heroImage.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
}

// ============ OBSERVE ANIMATIONS ON SCROLL ============
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animation = 'slideUpFade 0.8s ease-out forwards';
        }
    });
}, observerOptions);

// Observe hero right content
const heroRight = document.querySelector('.hero-right');
if (heroRight) {
    observer.observe(heroRight);
}

// Observe page 2 content
const page2Left = document.querySelector('.page2-left');
if (page2Left) {
    observer.observe(page2Left);
}

// Observe section 3
const section3Content = document.querySelector('.section3-content');
if (section3Content) {
    observer.observe(section3Content);
}

// Observe section 4 left
const section4Left = document.querySelector('.section4-left');
if (section4Left) {
    observer.observe(section4Left);
}

// Observe section 4 right
const section4Right = document.querySelector('.section4-right');
if (section4Right) {
    observer.observe(section4Right);
}

// Observe section 5 elements
const section5Header = document.querySelector('.section5-header');
const section5Left = document.querySelector('.section5-left');
const section5Right = document.querySelector('.section5-right');
const section5FooterSection = document.querySelector('.section5-footer-section');

if (section5Header) observer.observe(section5Header);
if (section5Left) observer.observe(section5Left);
if (section5Right) observer.observe(section5Right);
if (section5FooterSection) observer.observe(section5FooterSection);

// Observe footer
const footer = document.querySelector('.footer-container');
if (footer) observer.observe(footer);

// ============ ACCESSIBILITY - KEYBOARD NAVIGATION ============
document.querySelectorAll('.cta-button, .nav-link').forEach(element => {
    element.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            element.click();
        }
    });

    element.addEventListener('focus', function() {
        this.style.outline = '2px solid rgba(0, 113, 227, 0.5)';
        this.style.outlineOffset = '2px';
    });

    element.addEventListener('blur', function() {
        this.style.outline = 'none';
    });
});

// ============ PAGE LOAD ANIMATION ============
window.addEventListener('load', () => {
    document.body.style.opacity = '1';
    
    // Animate hero content on load
    const heroTitle = document.querySelector('.hero-title');
    const heroSubtitle = document.querySelector('.hero-subtitle');
    const ctaButton = document.querySelector('.cta-primary');
    
    if (heroTitle) heroTitle.style.animation = 'slideUpFade 0.8s ease-out 0.2s backwards';
    if (heroSubtitle) heroSubtitle.style.animation = 'slideUpFade 0.8s ease-out 0.3s backwards';
    if (ctaButton) ctaButton.style.animation = 'slideUpFade 0.8s ease-out 0.4s backwards';
});

// ============ LAZY LOADING IMAGE ============
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// ============ RIPPLE EFFECT CSS ============
const style = document.createElement('style');
style.textContent = `
    @keyframes rippleEffect {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// ============ CONSOLE MESSAGE ============
console.log('%cWildSpace', 'font-size: 20px; font-weight: bold; color: #000;');
console.log('%cStudy without the hassle of finding a space.', 'font-size: 14px; color: #555;');

// scroll reveal animation
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add("show");
        }
    });
});

document.querySelectorAll(".reveal").forEach((el) => observer.observe(el));

function closePopup() {
    const popup = document.getElementById("systemPopup");
    if (popup) {
        popup.classList.remove("active");
        popup.style.display = "none";
    }
}
