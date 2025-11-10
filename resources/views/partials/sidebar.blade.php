<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!-- Sidebar Brand -->
    <div class="sidebar-brand">
        <a href="{{ url('/') }}" class="brand-link">
            <img src="{{ asset('dist/assets/img/AdminLTELogo.png') }}" 
                 alt="AdminLTE Logo" 
                 class="brand-image opacity-75 shadow" 
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
            <i class="fas fa-chart-line brand-image" style="display: none; font-size: 28px; color: #007bff;"></i>
            <span class="brand-text fw-light">Sistem Admin</span>
        </a>
    </div>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('dist/assets/img/user2-160x160.jpg') }}" 
                     class="img-circle elevation-2" 
                     alt="User Image"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <i class="fas fa-user-circle" style="display: none; font-size: 34px; color: #adb5bd;"></i>
            </div>
            <div class="info">
                <a href="#" class="d-block text-white">Idham Khalid</a>
            </div>
        </div>
        
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <!-- Leaderboard -->
                <li class="nav-item">
                    <a href="{{ url('/leaderboard') }}" class="nav-link {{ request()->is('leaderboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-trophy"></i>
                        <p>Leaderboard</p>
                    </a>
                </li>
                
                <!-- Game Configuration with Submenu -->
                <li class="nav-item has-treeview {{ request()->is('admin/config*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('admin/config*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#configSubmenu" aria-expanded="{{ request()->is('admin/config*') ? 'true' : 'false' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Konfigurasi Game
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview collapse {{ request()->is('admin/config*') ? 'show' : '' }}" id="configSubmenu">
                        <li class="nav-item">
                            <a href="{{ url('/admin/config') }}" class="nav-link {{ request()->is('admin/config') && !request()->is('admin/config/edit') && !request()->is('admin/config/sync') ? 'active' : '' }}">
                                <i class="far fa-eye nav-icon text-info"></i>
                                <p>Lihat Konfigurasi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/config/edit') }}" class="nav-link {{ request()->is('admin/config/edit') ? 'active' : '' }}">
                                <i class="far fa-edit nav-icon text-warning"></i>
                                <p>Ubah Konfigurasi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/config/sync') }}" class="nav-link {{ request()->is('admin/config/sync') ? 'active' : '' }}">
                                <i class="fas fa-sync nav-icon text-success"></i>
                                <p>Sinkronisasi Versi</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Content Management with Submenu -->
                <li class="nav-item has-treeview {{ request()->is('admin/content*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('admin/content*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#contentSubmenu" aria-expanded="{{ request()->is('admin/content*') ? 'true' : 'false' }}">
                        <i class="nav-icon fas fa-puzzle-piece"></i>
                        <p>
                            Manajemen Konten Game
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview collapse {{ request()->is('admin/content*') ? 'show' : '' }}" id="contentSubmenu">
                        <li class="nav-item">
                            <a href="{{ url('/admin/content/scenarios') }}" class="nav-link {{ request()->is('admin/content/scenarios') ? 'active' : '' }}">
                                <i class="fas fa-list-alt nav-icon text-primary"></i>
                                <p>Skenario Pertanyaan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/content/cards') }}" class="nav-link {{ request()->is('admin/content/cards') ? 'active' : '' }}">
                                <i class="fas fa-id-card nav-icon text-success"></i>
                                <p>Kartu Risiko & Kesempatan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/content/quiz') }}" class="nav-link {{ request()->is('admin/content/quiz') ? 'active' : '' }}">
                                <i class="fas fa-question-circle nav-icon text-warning"></i>
                                <p>Kuis & Opsi</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Players Management -->
                <li class="nav-item">
                    <a href="{{ url('/admin/players') }}" class="nav-link {{ request()->is('admin/players') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Daftar Player</p>
                    </a>
                </li>

                <!-- Player Profiling is accessible from Daftar Player (inline), so separate search removed -->
                
            </ul>
        </nav>
    </div>
</aside>

<!-- Sidebar Menu Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle treeview menu functionality
    const treeviewMenus = document.querySelectorAll('[data-bs-toggle="collapse"]');
    
    treeviewMenus.forEach(function(menu) {
        menu.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('data-bs-target');
            const target = document.querySelector(targetId);
            const parentLi = this.closest('.nav-item');
            const arrow = this.querySelector('.fa-angle-left');
            
            if (target) {
                // Toggle the submenu
                if (target.classList.contains('show')) {
                    target.classList.remove('show');
                    parentLi.classList.remove('menu-open');
                    if (arrow) arrow.style.transform = '';
                } else {
                    // Close other open menus first
                    document.querySelectorAll('.nav-treeview.show').forEach(function(openMenu) {
                        if (openMenu !== target) {
                            openMenu.classList.remove('show');
                            openMenu.closest('.nav-item').classList.remove('menu-open');
                            const otherArrow = openMenu.closest('.nav-item').querySelector('.fa-angle-left');
                            if (otherArrow) otherArrow.style.transform = '';
                        }
                    });
                    
                    // Open this menu
                    target.classList.add('show');
                    parentLi.classList.add('menu-open');
                    if (arrow) arrow.style.transform = 'rotate(-90deg)';
                }
            }
        });
    });
    
    // Auto-open menu if current page is in submenu
    const currentPath = window.location.pathname;
    if (currentPath.includes('/admin/config')) {
        const configMenu = document.getElementById('configSubmenu');
        const configParent = configMenu.closest('.nav-item');
        const configArrow = configParent.querySelector('.fa-angle-left');
        
        if (configMenu && configParent) {
            configMenu.classList.add('show');
            configParent.classList.add('menu-open');
            if (configArrow) configArrow.style.transform = 'rotate(-90deg)';
        }
    }
    
    if (currentPath.includes('/admin/content')) {
        const contentMenu = document.getElementById('contentSubmenu');
        const contentParent = contentMenu.closest('.nav-item');
        const contentArrow = contentParent.querySelector('.fa-angle-left');
        
        if (contentMenu && contentParent) {
            contentMenu.classList.add('show');
            contentParent.classList.add('menu-open');
            if (contentArrow) contentArrow.style.transform = 'rotate(-90deg)';
        }
    }
});

// openPlayerProfiling removed: profiling is now accessed inline from Daftar Player page
</script>

<style>
/* Enhanced Sidebar Styling */
.nav-treeview {
    background-color: rgba(255,255,255,0.05);
    border-left: 3px solid #007bff;
    margin-left: 1rem;
    transition: all 0.3s ease;
}

.nav-treeview .nav-link {
    padding-left: 2rem;
    font-size: 0.9rem;
    border-radius: 0.25rem;
    margin: 0.1rem 0.5rem;
    transition: all 0.2s ease;
}

.nav-treeview .nav-link:hover {
    background-color: rgba(255,255,255,0.1);
    transform: translateX(5px);
}

.nav-treeview .nav-link.active {
    background-color: #007bff;
    color: white !important;
    box-shadow: 0 2px 4px rgba(0,123,255,0.3);
}

.has-treeview > .nav-link .fa-angle-left {
    transition: transform 0.3s ease;
}

.has-treeview.menu-open > .nav-link .fa-angle-left {
    transform: rotate(-90deg);
}

.nav-icon.text-info { color: #17a2b8 !important; }
.nav-icon.text-warning { color: #ffc107 !important; }
.nav-icon.text-success { color: #28a745 !important; }
</style>