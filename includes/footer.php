            </main>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="d-lg-none fixed-bottom bg-secondary border-top border-secondary py-2 px-3">
        <div class="d-flex justify-content-between text-center">
            <a href="index.php" class="text-secondary text-decoration-none small">
                <i class="fas fa-home d-block h5 mb-1 <?php echo ($active_page ?? '') == 'dashboard' ? 'text-accent' : ''; ?>"></i>
                Home
            </a>
            <a href="search.php" class="text-secondary text-decoration-none small">
                <i class="fas fa-search d-block h5 mb-1 <?php echo ($active_page ?? '') == 'search' ? 'text-accent' : ''; ?>"></i>
                Search
            </a>
            <a href="saved_lists.php" class="text-secondary text-decoration-none small">
                <i class="fas fa-list d-block h5 mb-1 <?php echo ($active_page ?? '') == 'lists' ? 'text-accent' : ''; ?>"></i>
                Lists
            </a>
            <a href="profile.php" class="text-secondary text-decoration-none small">
                <i class="fas fa-user d-block h5 mb-1 <?php echo ($active_page ?? '') == 'profile' ? 'text-accent' : ''; ?>"></i>
                Menu
            </a>
        </div>
    </nav>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    function toggleTheme() {
        const body = document.body;
        const currentTheme = body.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        body.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    }

    function updateThemeIcon(theme) {
        const icon = document.querySelector('#themeToggle i');
        if (icon) {
            icon.className = theme === 'light' ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    // Initialize theme
    const savedTheme = localStorage.getItem('theme') || 'dark';
    document.body.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);
    </script>
</body>
</html>
