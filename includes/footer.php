        </main>
    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> TeachMe Peer Tutoring System. All rights reserved.</p>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Contact Support</a>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <!-- Notification System -->
    <div id="notification-container" class="notification-container"></div>

    <!-- Loading Spinner -->
    <div id="loading-spinner" class="loading-spinner" style="display: none;">
        <div class="spinner"></div>
    </div>
</body>
</html>