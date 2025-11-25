    </main>
    
    <footer class="footer bg-light mt-5 py-4">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-12">
                    <?php 
                    // Get base path from global or determine it
                    $footerBasePath = $GLOBALS['basePath'] ?? ($basePath ?? '');
                    if (empty($footerBasePath)) {
                        $callingScript = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';
                        $isInPages = strpos($callingScript, '/pages/') !== false || strpos($callingScript, '\\pages\\') !== false;
                        $footerBasePath = $isInPages ? '../' : '';
                    }
                    ?>
                    <img src="<?php echo $footerBasePath; ?>assets/images/logo.svg" alt="Kyna English" height="40" class="mb-3" onerror="this.style.display='none'">
                    <p class="text-muted mb-2">
                        <i class="fas fa-phone text-success"></i> <strong>1900 6364 09</strong>
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-envelope text-danger"></i> <strong>hotro@kynaforkids.vn</strong>
                    </p>
                    <hr>
                    <p class="small text-muted mb-0">
                        &copy; <?php echo date('Y'); ?> Kyna English. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Suppress console errors from browser extensions -->
    <script>
    // Suppress errors from browser extensions that may interfere
    (function() {
        const originalError = console.error;
        console.error = function(...args) {
            // Filter out errors from browser extensions
            const errorMsg = args.join(' ');
            if (errorMsg.includes('metadata.js') || 
                errorMsg.includes('content.js') || 
                errorMsg.includes('onboarding.js') ||
                errorMsg.includes('chrome-extension://') ||
                errorMsg.includes('moz-extension://')) {
                // Suppress extension errors
                return;
            }
            // Log other errors normally
            originalError.apply(console, args);
        };
        
        // Handle unhandled promise rejections from extensions
        window.addEventListener('unhandledrejection', function(event) {
            const reason = event.reason?.toString() || '';
            if (reason.includes('metadata.js') || 
                reason.includes('content.js') || 
                reason.includes('onboarding.js') ||
                reason.includes('chrome-extension://') ||
                reason.includes('moz-extension://')) {
                event.preventDefault(); // Suppress extension errors
            }
        });
    })();
    </script>
    
    <!-- Custom JS -->
    <script src="<?php echo $footerBasePath ?? ''; ?>assets/js/main.js"></script>
    
    <?php if (isset($extraJS)) echo $extraJS; ?>
</body>
</html>

