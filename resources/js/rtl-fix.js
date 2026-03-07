// RTL Sidebar Fix for Mobile
document.addEventListener('DOMContentLoaded', function() {
    // Force RTL direction on all elements
    const forceRTL = () => {
        document.documentElement.setAttribute('dir', 'rtl');
        document.body.setAttribute('dir', 'rtl');
        
        // Force on all Filament containers
        document.querySelectorAll('[class*="fi-"]').forEach(el => {
            el.setAttribute('dir', 'rtl');
        });
    };
    
    // Fix sidebar transform direction for RTL on mobile
    const fixSidebarTransform = () => {
        const sidebar = document.querySelector('aside[class*="fi-sidebar"]');
        if (!sidebar) return;
        
        // Ensure RTL
        sidebar.setAttribute('dir', 'rtl');
        
        // Check if mobile view
        if (window.innerWidth <= 1024) {
            const isHidden = sidebar.getAttribute('aria-hidden') === 'true';
            
            // Apply correct transform based on state
            if (isHidden) {
                sidebar.style.setProperty('transform', 'translateX(100%)', 'important');
            } else {
                sidebar.style.setProperty('transform', 'translateX(0)', 'important');
            }
        }
    };
    
    // Apply fixes
    const applyFixes = () => {
        forceRTL();
        fixSidebarTransform();
    };
    
    // Watch for aria-hidden changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'aria-hidden') {
                fixSidebarTransform();
            }
        });
    });
    
    // Start observing
    const sidebar = document.querySelector('aside[class*="fi-sidebar"]');
    if (sidebar) {
        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['aria-hidden']
        });
    }
    
    // Initial fix
    applyFixes();
    
    // Re-fix on window resize
    window.addEventListener('resize', fixSidebarTransform);
    
    // Re-apply after Livewire navigation
    document.addEventListener('livewire:navigated', applyFixes);
    document.addEventListener('livewire:load', applyFixes);
    
    // Watch for new Filament elements being added
    const bodyObserver = new MutationObserver(() => {
        forceRTL();
    });
    
    bodyObserver.observe(document.body, {
        childList: true,
        subtree: true
    });
});
