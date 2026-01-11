// ==========================================
// NAVBAR SCROLL EFFECT
// ==========================================
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.main-navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// ==========================================
// MOBILE MENU TOGGLE
// ==========================================
const mobileMenuToggle = document.getElementById('mobileMenuToggle');
const mobileMenu = document.getElementById('mobileMenu');

if (mobileMenuToggle && mobileMenu) {
    mobileMenuToggle.addEventListener('click', function() {
        mobileMenu.classList.toggle('active');
        this.classList.toggle('active');
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!mobileMenuToggle.contains(event.target) && !mobileMenu.contains(event.target)) {
            mobileMenu.classList.remove('active');
            mobileMenuToggle.classList.remove('active');
        }
    });

    // Close menu when clicking on a link
    const mobileMenuItems = mobileMenu.querySelectorAll('.mobile-menu-item');
    mobileMenuItems.forEach(item => {
        item.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            mobileMenuToggle.classList.remove('active');
        });
    });
}

// ==========================================
// SWAP DEPARTURE AND ARRIVAL
// ==========================================
const swapBtn = document.querySelector('.swap-btn');
if (swapBtn) {
    swapBtn.addEventListener('click', function() {
        const departInput = document.querySelector('input[name="depart"]');
        const arriveeInput = document.querySelector('input[name="arrivee"]');
        
        if (departInput && arriveeInput) {
            const temp = departInput.value;
            departInput.value = arriveeInput.value;
            arriveeInput.value = temp;
            
            // Add animation
            this.style.transform = 'translateY(-50%) rotate(180deg)';
            setTimeout(() => {
                this.style.transform = 'translateY(-50%) rotate(0deg)';
            }, 300);
        }
    });
}

// ==========================================
// AUTO-HIDE FLASH MESSAGES
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const flashMessages = document.querySelectorAll('.alert-custom');
    
    flashMessages.forEach(function(message) {
        setTimeout(function() {
            message.style.animation = 'slideOutRight 0.4s ease';
            setTimeout(function() {
                message.parentElement.remove();
            }, 400);
        }, 5000);
    });
});

// ==========================================
// SMOOTH SCROLL FOR ANCHOR LINKS
// ==========================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href !== '') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                const offset = 80; // Height of fixed navbar
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        }
    });
});

// ==========================================
// FORM VALIDATION ENHANCEMENT
// ==========================================
const forms = document.querySelectorAll('form');
forms.forEach(form => {
    form.addEventListener('submit', function(e) {
        const inputs = this.querySelectorAll('input[required], select[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.style.borderColor = '#EF4444';
                
                setTimeout(() => {
                    input.style.borderColor = '';
                }, 2000);
            }
        });
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});

// ==========================================
// DATE INPUT MIN VALUE (TODAY)
// ==========================================
const dateInputs = document.querySelectorAll('input[type="date"]');
const today = new Date().toISOString().split('T')[0];

dateInputs.forEach(input => {
    if (!input.hasAttribute('min')) {
        input.setAttribute('min', today);
    }
});

// ==========================================
// INTERSECTION OBSERVER FOR ANIMATIONS
// ==========================================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animation = 'fadeInUp 0.6s ease forwards';
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe elements
const animatedElements = document.querySelectorAll(
    '.route-card, .feature-card, .review-card, .trust-badge'
);

animatedElements.forEach(el => {
    el.style.opacity = '0';
    observer.observe(el);
});

// ==========================================
// PREVENT DOUBLE FORM SUBMISSION
// ==========================================
forms.forEach(form => {
    let isSubmitting = false;
    
    form.addEventListener('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        
        isSubmitting = true;
        
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
            
            // Reset after 5 seconds (in case of error)
            setTimeout(() => {
                isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 5000);
        }
    });
});

// ==========================================
// ADD SLIDEOUT ANIMATION FOR ALERTS
// ==========================================
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100px);
        }
    }
`;
document.head.appendChild(styleSheet);