<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fixsmith · PC Repair Studio</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="stylesheet" href="/css/fixsmith.css">
    <style>
        /* Extra styles for SMTP modal & toaster */
        .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 100; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
        .toast { pointer-events: auto; min-width: 280px; max-width: 420px; padding: 14px 18px; border-radius: 12px; background: #17211b; color: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 12px; font-size: 13px; animation: toast-in 0.25s ease-out; }
        .toast.success { background: #103b2a; border-left: 4px solid #b8ddc8; }
        .toast.error { background: #571c19; border-left: 4px solid #fae8e6; }
        @keyframes toast-in { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .smtp-badge-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 9px; border: 1px solid var(--line); background: white; color: var(--forest); font-size: 11px; font-weight: 700; cursor: pointer; transition: all .15s; }
        .smtp-badge-btn:hover { background: var(--mint); }
        .smtp-tab-nav { display: flex; gap: 6px; border-bottom: 1px solid var(--line); padding: 0 24px; margin-bottom: 20px; }
        .smtp-tab-btn { padding: 12px 16px; border: none; background: transparent; font-size: 12px; font-weight: 700; color: var(--muted); cursor: pointer; border-bottom: 2px solid transparent; }
        .smtp-tab-btn.active { color: var(--forest); border-bottom-color: var(--forest); }
        .log-table { width: 100%; font-size: 11px; border-collapse: collapse; }
        .log-table th { background: var(--surface-soft); padding: 8px 12px; text-align: left; }
        .log-table td { padding: 10px 12px; border-top: 1px solid var(--line); }
        .log-badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: 700; text-transform: uppercase; }
        .log-badge.sent { background: #dceee4; color: #174c36; }
        .log-badge.logged { background: #e4eef7; color: #3d6fa0; }
        .log-badge.failed { background: #fae8e6; color: #ad4f4a; }
        .team-job-reassign { margin-top: 6px; padding: 3px 6px; font-size: 9px; border: 1px solid var(--line); border-radius: 6px; width: 100%; }
        [v-cloak] { display: none !important; }
    </style>
</head>
<body>

<svg style="display: none;">
    <defs>
        <symbol id="icon-activity" viewBox="0 0 24 24"><path d="M4 12h3l2-6 4 12 2-6h5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-arrow" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-bell" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M13.7 21a2 2 0 0 1-3.4 0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-box" viewBox="0 0 24 24"><path d="m21 8-9 5-9-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="m3 8 9-5 9 5v8l-9 5-9-5Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 13v8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-calendar" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 3v4M8 3v4M3 10h18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-check" viewBox="0 0 24 24"><path d="m5 12 4 4L19 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-chevron" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 7v5l3 2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-computer" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 21h8M12 17v4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-dashboard" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><rect x="14" y="3" width="7" height="7" rx="1" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><rect x="3" y="14" width="7" height="7" rx="1" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><rect x="14" y="14" width="7" height="7" rx="1" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-edit" viewBox="0 0 24 24"><path d="M12 20h9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-file" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6M8 13h8M8 17h6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-inbox" viewBox="0 0 24 24"><path d="M4 4h16v16H4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 14h5l2 3h2l2-3h5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 11v5M12 8h.01" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-logout" viewBox="0 0 24 24"><path d="M10 17l5-5-5-5M15 12H3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-mail" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="m3 7 9 6 9-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-map" viewBox="0 0 24 24"><path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 3v15M15 6v15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-menu" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-phone" viewBox="0 0 24 24"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.8a2 2 0 0 1-.4 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4 13 13 0 0 0 2.8.7 2 2 0 0 1 1.7 2Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-pause" viewBox="0 0 24 24"><path d="M9 5v14M15 5v14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-play" viewBox="0 0 24 24"><path d="m8 5 11 7-11 7Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-profile" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 21a8 8 0 0 1 16 0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="m20 20-4-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-settings" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4V21a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3A1.7 1.7 0 0 0 10 3V2.8h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-star" viewBox="0 0 24 24"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-2.9-5.6 2.9 1.1-6.2L3 9.6l6.2-.9Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-tool" viewBox="0 0 24 24"><path d="M14.7 6.3a4 4 0 0 0-5-5L12 3.6 8.6 7 6.3 4.7a4 4 0 0 0 5 5L4 17l3 3 7.3-7.3a4 4 0 0 0 5-5L17 10l-3.4-3.4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-upload" viewBox="0 0 24 24"><path d="M12 16V4M7 9l5-5 5 5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 14v5h14v-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="icon-users" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="7" r="4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    </defs>
</svg>

<div id="app" class="app-shell" v-cloak>
    <!-- AUTH SCREEN -->
    <template v-if="!authenticated">
        <main class="auth-shell">
            <section class="auth-story">
                <div class="auth-brand">
                    <span class="logo-mark"><svg class="icon" width="20" height="20"><use href="#icon-tool"></use></svg></span>
                    <span><strong>Fixsmith</strong><small>PC repair studio</small></span>
                </div>
                <div class="auth-story-content">
                    <span class="auth-eyebrow">Repair care, made clear</span>
                    <h1>Your PC repair journey, all in one place.</h1>
                    <p>Submit a repair, follow every update, and stay connected with the workshop from diagnosis to done with automated SMTP email notifications.</p>
                    <div class="auth-benefits">
                        <div><span><svg class="icon" width="18" height="18"><use href="#icon-activity"></use></svg></span><div><strong>Live repair tracking</strong><p>See each stage as your device moves through the workshop.</p></div></div>
                        <div><span><svg class="icon" width="18" height="18"><use href="#icon-mail"></use></svg></span><div><strong>Instant SMTP Email Notifications</strong><p>Receive real-time email alerts for diagnosis, parts, and completion.</p></div></div>
                        <div><span><svg class="icon" width="18" height="18"><use href="#icon-check"></use></svg></span><div><strong>Everything documented</strong><p>Keep requests, updates, and feedback together in one account.</p></div></div>
                    </div>
                </div>
                <div class="auth-trust">
                    <div class="auth-avatar-stack"><span>AK</span><span>SR</span><span>PS</span></div>
                    <p><strong>Trusted technicians</strong><br>Ready to help, six days a week</p>
                </div>
            </section>

            <section class="auth-form-side">
                <div class="auth-mobile-brand">
                    <span class="logo-mark"><svg class="icon" width="18" height="18"><use href="#icon-tool"></use></svg></span>
                    <strong>Fixsmith</strong>
                </div>
                <form class="auth-form" @submit.prevent="handleAuthSubmit">
                    <div class="auth-form-heading">
                        <span class="kicker">@{{ authMode === 'register' ? 'Create your account' : 'Welcome back' }}</span>
                        <h2>@{{ authMode === 'register' ? 'Get started with Fixsmith' : 'Sign in to your account' }}</h2>
                        <p>@{{ authMode === 'register' ? 'Create a customer account to request and track PC repairs.' : 'Enter your details to access your repair workspace.' }}</p>
                    </div>

                    <div v-if="authError" class="auth-error">@{{ authError }}</div>

                    <label v-if="authMode === 'register'" class="field">
                        <span>Full name</span>
                        <input v-model="authForm.name" placeholder="Enter your full name" required>
                    </label>

                    <label class="field">
                        <span>Email address</span>
                        <input type="email" v-model="authForm.email" placeholder="name@example.com" required>
                    </label>

                    <label v-if="authMode === 'register'" class="field">
                        <span>Telephone number</span>
                        <input type="tel" v-model="authForm.phone" placeholder="+1 (202) 555-0100" required>
                    </label>

                    <label class="field">
                        <span>@{{ authMode === 'register' ? 'Create password' : 'Password' }}</span>
                        <input type="password" v-model="authForm.password" :placeholder="authMode === 'register' ? 'At least 8 characters' : 'Enter your password'" required>
                    </label>

                    <label v-if="authMode === 'register'" class="field">
                        <span>Confirm password</span>
                        <input type="password" v-model="authForm.confirmPassword" placeholder="Enter your password again" required>
                    </label>

                    <div v-if="authMode === 'login'" class="auth-options">
                        <label><input type="checkbox" v-model="authForm.remember"> Remember me</label>
                        <button type="button" @click="showToast('Password reset link sent to registered email.', 'success')">Forgot password?</button>
                    </div>

                    <button type="submit" class="button button-primary">
                        <svg class="icon" width="16" height="16"><use href="#icon-arrow"></use></svg>
                        @{{ authMode === 'register' ? 'Create customer account' : 'Sign in' }}
                    </button>

                    <div class="auth-divider"><span>or</span></div>

                    <p class="auth-switch">
                        @{{ authMode === 'register' ? 'Already have an account?' : 'New to Fixsmith?' }}
                        <button type="button" @click="toggleAuthMode">
                            @{{ authMode === 'register' ? 'Sign in' : 'Create an account' }}
                        </button>
                    </p>

                    <div v-if="authMode === 'login'" class="role-preview">
                        <div>
                            <span class="kicker">Preview without login</span>
                            <p>Open any workspace instantly.</p>
                        </div>
                        <div class="role-preview-buttons">
                            <button type="button" @click="quickLogin('customer')"><svg class="icon" width="14" height="14"><use href="#icon-profile"></use></svg><span>Customer</span></button>
                            <button type="button" @click="quickLogin('admin')"><svg class="icon" width="14" height="14"><use href="#icon-dashboard"></use></svg><span>Admin</span></button>
                            <button type="button" @click="quickLogin('technician')"><svg class="icon" width="14" height="14"><use href="#icon-tool"></use></svg><span>Technician</span></button>
                        </div>
                    </div>
                </form>
                <p class="auth-legal">By continuing, you agree to Fixsmith’s Terms of Service and Privacy Policy.</p>
            </section>
        </main>
    </template>

    <!-- AUTHENTICATED APP WORKSPACE -->
    <template v-else>
        <!-- SIDEBAR -->
        <div v-if="sidebarOpen" class="sidebar-scrim" @click="sidebarOpen = false"></div>
        <aside class="sidebar" :class="{ open: sidebarOpen }">
            <div class="brand">
                <span class="logo-mark"><svg class="icon" width="20" height="20"><use href="#icon-tool"></use></svg></span>
                <span><strong>Fixsmith</strong><small>PC repair studio</small></span>
            </div>

            <nav>
                <p class="nav-label">Workspace</p>

                <!-- Customer Navigation -->
                <template v-if="currentRole === 'customer'">
                    <button class="nav-item" :class="{ active: customerTab === 'overview' }" @click="navigateCustomer('overview')">
                        <svg class="icon" width="16" height="16"><use href="#icon-dashboard"></use></svg>Overview
                    </button>
                    <button class="nav-item" :class="{ active: customerTab === 'new-repair' }" @click="navigateCustomer('new-repair')">
                        <svg class="icon" width="16" height="16"><use href="#icon-plus"></use></svg>New repair
                    </button>
                    <button class="nav-item" :class="{ active: customerTab === 'repairs' }" @click="navigateCustomer('repairs')">
                        <svg class="icon" width="16" height="16"><use href="#icon-clock"></use></svg>My repairs
                        <span class="nav-count">@{{ customerRepairs.length }}</span>
                    </button>
                    <button class="nav-item" :class="{ active: customerTab === 'complaint' }" @click="navigateCustomer('complaint')">
                        <svg class="icon" width="16" height="16"><use href="#icon-star"></use></svg>Rate &amp; complain
                    </button>
                    <button class="nav-item" :class="{ active: customerTab === 'profile' }" @click="navigateCustomer('profile')">
                        <svg class="icon" width="16" height="16"><use href="#icon-profile"></use></svg>Profile
                    </button>
                </template>

                <!-- Admin Navigation -->
                <template v-if="currentRole === 'admin'">
                    <button class="nav-item" :class="{ active: adminTab === 'dashboard' }" @click="navigateAdmin('dashboard')">
                        <svg class="icon" width="16" height="16"><use href="#icon-dashboard"></use></svg>Dashboard
                    </button>
                    <button class="nav-item" :class="{ active: adminTab === 'requests' }" @click="navigateAdmin('requests')">
                        <svg class="icon" width="16" height="16"><use href="#icon-inbox"></use></svg>Repair requests
                        <span class="nav-count">@{{ repairs.length }}</span>
                    </button>
                    <button class="nav-item" :class="{ active: adminTab === 'accounts' }" @click="navigateAdmin('accounts')">
                        <svg class="icon" width="16" height="16"><use href="#icon-users"></use></svg>Accounts
                    </button>
                    <button class="nav-item" :class="{ active: adminTab === 'team' }" @click="navigateAdmin('team')">
                        <svg class="icon" width="16" height="16"><use href="#icon-tool"></use></svg>Manage team
                    </button>
                    <button class="nav-item" :class="{ active: adminTab === 'complaints' }" @click="navigateAdmin('complaints')">
                        <svg class="icon" width="16" height="16"><use href="#icon-star"></use></svg>Customer feedback
                        <span class="nav-count">@{{ feedbackList.length }}</span>
                    </button>
                    <button class="nav-item" :class="{ active: adminTab === 'profile' }" @click="navigateAdmin('profile')">
                        <svg class="icon" width="16" height="16"><use href="#icon-profile"></use></svg>Profile
                    </button>
                </template>

                <!-- Technician Navigation -->
                <template v-if="currentRole === 'technician'">
                    <button class="nav-item" :class="{ active: technicianTab === 'workspace' }" @click="navigateTechnician('workspace')">
                        <svg class="icon" width="16" height="16"><use href="#icon-dashboard"></use></svg>My workspace
                    </button>
                    <button class="nav-item" :class="{ active: technicianTab === 'assigned' }" @click="navigateTechnician('assigned')">
                        <svg class="icon" width="16" height="16"><use href="#icon-tool"></use></svg>Assigned repairs
                        <span class="nav-count">@{{ technicianAssignedRepairs.length }}</span>
                    </button>
                    <button class="nav-item" :class="{ active: technicianTab === 'complaints' }" @click="navigateTechnician('complaints')">
                        <svg class="icon" width="16" height="16"><use href="#icon-star"></use></svg>Customer feedback
                    </button>
                    <button class="nav-item" :class="{ active: technicianTab === 'profile' }" @click="navigateTechnician('profile')">
                        <svg class="icon" width="16" height="16"><use href="#icon-profile"></use></svg>Profile
                    </button>
                </template>
            </nav>

            <div class="sidebar-bottom">
                <button class="smtp-badge-btn" @click="smtpModalOpen = true" style="margin: 0 4px 8px; justify-content: center;">
                    <svg class="icon" width="15" height="15"><use href="#icon-mail"></use></svg>
                    <span>SMTP Notifications</span>
                </button>
                <div class="support-card">
                    <span class="support-icon"><svg class="icon" width="16" height="16"><use href="#icon-phone"></use></svg></span>
                    <strong>Need help?</strong>
                    <p>Call our workshop</p>
                    <a href="tel:+12025550148">+1 (202) 555-0148</a>
                </div>
                <button class="nav-item logout" @click="logout">
                    <svg class="icon" width="16" height="16"><use href="#icon-logout"></use></svg>Log out
                </button>
            </div>
        </aside>

        <!-- NOTIFICATION DRAWER -->
        <button v-if="notificationOpen" class="notification-scrim" @click="notificationOpen = false" aria-label="Close notifications"></button>
        <aside class="notification-drawer" :class="{ open: notificationOpen }">
            <div class="notification-head">
                <div>
                    <span class="kicker">Activity center</span>
                    <h2>Notifications</h2>
                    <p>@{{ unreadNotificationCount > 0 ? unreadNotificationCount + ' unread updates' : "You're all caught up" }}</p>
                </div>
                <button class="modal-close" @click="notificationOpen = false">&times;</button>
            </div>
            <div class="notification-tools">
                <button @click="markAllNotificationsRead">
                    <svg class="icon" width="13" height="13"><use href="#icon-check"></use></svg>Mark all as read
                </button>
                <button @click="smtpModalOpen = true">
                    <svg class="icon" width="13" height="13"><use href="#icon-mail"></use></svg>SMTP Settings
                </button>
            </div>

            <div class="notification-section">
                <div class="notification-section-title"><span>Upcoming</span><small>Scheduled reminders</small></div>
                <template v-for="item in upcomingNotifications" :key="item.id">
                    <button class="notification-item upcoming" @click="handleNotificationClick(item)">
                        <span class="notification-icon"><svg class="icon" width="16" height="16"><use :href="'#icon-' + item.icon"></use></svg></span>
                        <span>
                            <strong>@{{ item.title }}</strong>
                            <p>@{{ item.message }}</p>
                            <small><svg class="icon" width="12" height="12"><use href="#icon-calendar"></use></svg>@{{ item.time_label }}</small>
                        </span>
                        <i v-if="!item.is_read"></i>
                    </button>
                </template>
            </div>

            <div class="notification-section">
                <div class="notification-section-title"><span>Recent</span><small>Latest account activity</small></div>
                <template v-for="item in recentNotifications" :key="item.id">
                    <button class="notification-item" :class="{ read: item.is_read }" @click="handleNotificationClick(item)">
                        <span class="notification-icon"><svg class="icon" width="16" height="16"><use :href="'#icon-' + item.icon"></use></svg></span>
                        <span>
                            <strong>@{{ item.title }}</strong>
                            <p>@{{ item.message }}</p>
                            <small><svg class="icon" width="12" height="12"><use href="#icon-clock"></use></svg>@{{ item.time_label }}</small>
                        </span>
                        <i v-if="!item.is_read"></i>
                    </button>
                </template>
            </div>

            <div class="notification-footer">
                <svg class="icon" width="16" height="16"><use href="#icon-bell"></use></svg>
                <p>Future repair updates and reminders will appear here automatically and trigger SMTP email alerts.</p>
            </div>
        </aside>

        <!-- MAIN SHELL -->
        <div class="main-shell">
            <header class="topbar">
                <button class="mobile-menu" @click="sidebarOpen = true" aria-label="Open navigation">
                    <svg class="icon" width="18" height="18"><use href="#icon-menu"></use></svg>
                </button>
                <div class="mobile-brand">
                    <span class="logo-mark"><svg class="icon" width="16" height="16"><use href="#icon-tool"></use></svg></span>
                    <strong>Fixsmith</strong>
                </div>

                <!-- LIVE ROLE SWITCHER -->
                <div class="role-switch" aria-label="Preview role">
                    <button :class="{ active: currentRole === 'customer' }" @click="switchRole('customer')">Customer</button>
                    <button :class="{ active: currentRole === 'admin' }" @click="switchRole('admin')">Admin</button>
                    <button :class="{ active: currentRole === 'technician' }" @click="switchRole('technician')">Technician</button>
                </div>

                <div class="header-actions">
                    <button class="smtp-badge-btn" @click="smtpModalOpen = true" title="Configure SMTP and Test Emails">
                        <svg class="icon" width="14" height="14"><use href="#icon-mail"></use></svg>
                        <span>SMTP Email</span>
                    </button>

                    <button class="icon-button" aria-label="Open notifications" @click="notificationOpen = true">
                        <svg class="icon" width="18" height="18"><use href="#icon-bell"></use></svg>
                        <span v-if="unreadNotificationCount > 0" class="notification-dot"></span>
                    </button>

                    <div style="position: relative;">
                        <button class="profile-trigger" @click="profileMenuOpen = !profileMenuOpen">
                            <span class="avatar">@{{ currentUser.avatar_initials || 'FX' }}</span>
                            <span class="user-meta">
                                <strong>@{{ currentUser.name }}</strong>
                                <span>@{{ currentRole === 'admin' ? 'Administrator' : (currentRole === 'technician' ? 'Senior technician' : 'Customer') }}</span>
                            </span>
                            <svg class="icon" width="15" height="15"><use href="#icon-chevron"></use></svg>
                        </button>

                        <div v-if="profileMenuOpen" class="profile-menu">
                            <button @click="openProfile(); profileMenuOpen = false">
                                <svg class="icon" width="16" height="16"><use href="#icon-profile"></use></svg>
                                <span><strong>Profile</strong><small>Edit your personal details</small></span>
                            </button>
                            <button @click="smtpModalOpen = true; profileMenuOpen = false">
                                <svg class="icon" width="16" height="16"><use href="#icon-mail"></use></svg>
                                <span><strong>SMTP Notification Settings</strong><small>Configure email delivery</small></span>
                            </button>
                            <button class="menu-logout" @click="logout; profileMenuOpen = false">
                                <svg class="icon" width="16" height="16"><use href="#icon-logout"></use></svg>
                                <span><strong>Log out</strong><small>End your current session</small></span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <main>
                <!-- CUSTOMER VIEW -->
                <template v-if="currentRole === 'customer'">
                    <!-- Overview -->
                    <div v-if="customerTab === 'overview'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Customer portal</p>
                                <h1>Good morning, @{{ currentUser.name.split(' ')[0] }}</h1>
                                <p>Start a new repair or check in on your current device.</p>
                            </div>
                            <button class="button button-primary" @click="navigateCustomer('new-repair')">
                                <svg class="icon" width="16" height="16"><use href="#icon-plus"></use></svg>Start a repair
                            </button>
                        </div>

                        <section class="summary-grid">
                            <div class="summary-card featured" @click="navigateCustomer('repairs')" style="cursor: pointer;">
                                <span class="summary-icon"><svg class="icon" width="20" height="20"><use href="#icon-tool"></use></svg></span>
                                <div>
                                    <p>Active repair</p>
                                    <strong>@{{ customerRepairs[0] ? customerRepairs[0].status : 'No active repairs' }}</strong>
                                    <small>@{{ customerRepairs[0] ? customerRepairs[0].device : 'Submit a ticket below' }}</small>
                                </div>
                                <svg class="icon" width="16" height="16"><use href="#icon-arrow"></use></svg>
                            </div>
                            <div class="summary-card">
                                <span class="summary-icon orange"><svg class="icon" width="20" height="20"><use href="#icon-clock"></use></svg></span>
                                <div>
                                    <p>Estimated update</p>
                                    <strong>Tomorrow</strong>
                                    <small>By 5:00 PM</small>
                                </div>
                            </div>
                            <div class="summary-card">
                                <span class="summary-icon green"><svg class="icon" width="20" height="20"><use href="#icon-check"></use></svg></span>
                                <div>
                                    <p>Repairs completed</p>
                                    <strong>@{{ customerCompletedCount }} devices</strong>
                                    <small>Tracked in Fixsmith</small>
                                </div>
                            </div>
                        </section>

                        <!-- REPAIR PROGRESS CAROUSEL -->
                        <section v-if="customerRepairs.length > 0" class="panel tracking-panel carousel-panel">
                            <div class="carousel-toolbar">
                                <div>
                                    <span class="kicker">Repair progress</span>
                                    <span class="carousel-count">@{{ carouselIndex + 1 }} of @{{ customerRepairs.length }}</span>
                                </div>
                                <div class="carousel-controls">
                                    <button class="carousel-button previous" @click="prevCarousel" aria-label="Previous repair">
                                        <svg class="icon" width="16" height="16"><use href="#icon-arrow"></use></svg>
                                    </button>
                                    <button class="carousel-button" @click="nextCarousel" aria-label="Next repair">
                                        <svg class="icon" width="16" height="16"><use href="#icon-arrow"></use></svg>
                                    </button>
                                </div>
                            </div>

                            <div v-if="activeCarouselRepair" class="carousel-content" :key="activeCarouselRepair.id">
                                <div class="panel-head">
                                    <div>
                                        <span class="kicker">Repair #@{{ activeCarouselRepair.id.replace('FX-', '') }}</span>
                                        <h2>@{{ activeCarouselRepair.status === 'Finished' ? 'Repair completed' : 'Repair in progress' }}</h2>
                                    </div>
                                    <span :class="'status-pill status-' + activeCarouselRepair.status.toLowerCase().replaceAll(' ', '-')">
                                        <span class="status-dot"></span>@{{ activeCarouselRepair.status }}
                                    </span>
                                </div>

                                <div class="device-strip">
                                    <span class="device-icon"><svg class="icon" width="24" height="24"><use href="#icon-computer"></use></svg></span>
                                    <div>
                                        <strong>@{{ activeCarouselRepair.device }}</strong>
                                        <p>Serial: @{{ activeCarouselRepair.serial }} &middot; @{{ activeCarouselRepair.method }} service</p>
                                    </div>
                                    <button class="button button-ghost" @click="navigateCustomer('repairs', activeCarouselRepair.id)">View details</button>
                                </div>

                                <div class="timeline">
                                    <template v-for="(stage, sIndex) in getTimelineStages(activeCarouselRepair)" :key="stage">
                                        <div class="timeline-step" :class="{ done: sIndex <= getRepairActiveStep(activeCarouselRepair), current: sIndex === getRepairActiveStep(activeCarouselRepair) && activeCarouselRepair.status !== 'Finished' }">
                                            <span class="timeline-node">
                                                <svg v-if="sIndex <= getRepairActiveStep(activeCarouselRepair)" class="icon" width="13" height="13"><use href="#icon-check"></use></svg>
                                                <template v-else>@{{ sIndex + 1 }}</template>
                                            </span>
                                            <div>
                                                <strong>@{{ stage }}</strong>
                                                <small>@{{ sIndex < getRepairActiveStep(activeCarouselRepair) ? 'Completed' : (sIndex === getRepairActiveStep(activeCarouselRepair) ? (activeCarouselRepair.status === 'Finished' ? 'Completed' : 'In progress') : 'Upcoming') }}</small>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <div class="update-note">
                                    <svg class="icon" width="18" height="18"><use href="#icon-activity"></use></svg>
                                    <div>
                                        <strong>@{{ activeCarouselRepair.status === 'Finished' ? 'Final update' : 'Latest workshop note' }}</strong>
                                        <p>@{{ activeCarouselRepair.latest_update }}</p>
                                    </div>
                                    <span>@{{ activeCarouselRepair.created_date_formatted }}</span>
                                </div>
                            </div>

                            <div class="carousel-footer">
                                <button class="autoplay-button" @click="carouselPaused = !carouselPaused">
                                    <svg class="icon" width="14" height="14"><use :href="carouselPaused ? '#icon-play' : '#icon-pause'"></use></svg>
                                    @{{ carouselPaused ? 'Resume auto-play' : 'Stop auto-play' }}
                                </button>
                                <div class="carousel-dots" :class="{ paused: carouselPaused }">
                                    <button v-for="(item, dIndex) in customerRepairs" :key="item.id" :class="{ active: dIndex === carouselIndex }" @click="carouselIndex = dIndex">
                                        <span></span>
                                    </button>
                                </div>
                                <span class="carousel-footer-spacer"></span>
                            </div>
                        </section>
                    </div>

                    <!-- New Repair Form -->
                    <div v-if="customerTab === 'new-repair'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">New repair</p>
                                <h1>Tell us about your PC</h1>
                                <p>Share a few details and we’ll take it from here. An automated confirmation email will be sent immediately.</p>
                            </div>
                        </div>

                        <form class="panel form-panel standalone-card" @submit.prevent="submitNewRepair">
                            <div class="panel-head">
                                <div>
                                    <span class="kicker">Repair request</span>
                                    <h2>Device details</h2>
                                    <p>Give our technicians enough detail to prepare for your repair.</p>
                                </div>
                                <span class="step-badge">01</span>
                            </div>

                            <label class="field">
                                <span>Device Model / Name (Optional)</span>
                                <input v-model="newRepairForm.device" placeholder="e.g. ASUS ROG Zephyrus G14, Custom Desktop PC">
                            </label>

                            <label class="field">
                                <span>PC serial number</span>
                                <input v-model="newRepairForm.serial" placeholder="e.g. M7NRKD029184" required>
                            </label>

                            <label class="field">
                                <span>What’s going wrong?</span>
                                <textarea v-model="newRepairForm.symptom" placeholder="Describe the symptoms, when they started, and anything you’ve already tried." required></textarea>
                            </label>

                            <div class="upload-group">
                                <label style="display:flex;justify-content:space-between;align-items:center;font-size:11px;font-weight:700;">
                                    <span>PC Symptom Photos (Max 5)</span>
                                    <span class="upload-counter" :class="{ max: newRepairForm.photoFiles.length >= 5 }">
                                        @{{ newRepairForm.photoFiles.length }} / 5 photos uploaded
                                    </span>
                                </label>
                                <div class="upload-zone" @click="newRepairForm.photoFiles.length < 5 && $refs.photoInput.click()">
                                    <input ref="photoInput" type="file" accept="image/png,image/jpeg,image/gif,image/webp,image/*" multiple @change="handlePhotoUpload">
                                    <span><svg class="icon" width="18" height="18"><use :href="newRepairForm.photoFiles.length > 0 ? '#icon-check' : '#icon-upload'"></use></svg></span>
                                    <div>
                                        <strong>@{{ newRepairForm.photoFiles.length > 0 ? (newRepairForm.photoFiles.length >= 5 ? 'Maximum 5 photos reached' : 'Add more photos (' + (5 - newRepairForm.photoFiles.length) + ' remaining)') : 'Upload symptom photos or GIF animations' }}</strong>
                                        <p>@{{ newRepairForm.photoFiles.length >= 5 ? 'All 5 photo slots filled. Click remove (×) on any photo to replace.' : 'Select up to 5 PNG, JPG, or GIF files (memes welcome!) &middot; Click to browse' }}</p>
                                    </div>
                                </div>

                                <!-- Symptom Preview Grid -->
                                <div v-if="newRepairForm.photoFiles.length > 0" class="symptom-preview-grid">
                                    <div v-for="(img, idx) in newRepairForm.photoFiles" :key="idx" class="symptom-preview-card">
                                        <span v-if="isGif(img.name || img.preview)" class="gif-badge">GIF</span>
                                        <img :src="img.preview" :alt="'Symptom ' + (idx + 1)" @click="openLightbox(img.preview, 'Symptom Photo ' + (idx + 1) + ': ' + img.name)">
                                        <span class="thumb-badge">Photo @{{ idx + 1 }}</span>
                                        <button type="button" class="remove-btn" @click.stop="removeSymptomPhoto(idx)" title="Remove this photo">&times;</button>
                                    </div>
                                </div>
                            </div>

                            <fieldset class="delivery-fieldset">
                                <legend>How will your device reach us?</legend>
                                <label class="delivery-option" :class="{ selected: newRepairForm.delivery === 'mail' }">
                                    <input type="radio" name="delivery" value="mail" v-model="newRepairForm.delivery">
                                    <span class="radio-mark"></span>
                                    <svg class="icon" width="18" height="18"><use href="#icon-mail"></use></svg>
                                    <div>
                                        <strong>Mail to workshop</strong>
                                        <p>Ship it to us using a tracked courier</p>
                                    </div>
                                </label>
                                <label class="delivery-option" :class="{ selected: newRepairForm.delivery === 'dropoff' }">
                                    <input type="radio" name="delivery" value="dropoff" v-model="newRepairForm.delivery">
                                    <span class="radio-mark"></span>
                                    <svg class="icon" width="18" height="18"><use href="#icon-map"></use></svg>
                                    <div>
                                        <strong>Bring it yourself</strong>
                                        <p>Drop off at our downtown workshop</p>
                                    </div>
                                </label>
                            </fieldset>

                            <!-- Shipping Slip Section (Required for Mail-in) -->
                            <div v-if="newRepairForm.delivery === 'mail'" class="upload-group">
                                <div class="slip-upload">
                                    <div>
                                        <svg class="icon" width="18" height="18"><use href="#icon-file"></use></svg>
                                        <span>
                                            <strong>Courier shipping slip <span style="color:#ef4444;font-size:10px;">*Required for Mail-in</span></strong>
                                            <small>Upload your courier receipt, postal tracking label, or drop-off ticket.</small>
                                        </span>
                                    </div>
                                    <input ref="slipInput" type="file" accept="image/png,image/jpeg,image/gif,image/webp,.pdf,image/*" @change="handleSlipUpload" style="display:none;">
                                    <button type="button" class="button button-secondary" @click="$refs.slipInput.click()">
                                        <svg class="icon" width="14" height="14"><use :href="newRepairForm.slipFile ? '#icon-check' : '#icon-upload'"></use></svg>
                                        @{{ newRepairForm.slipFile ? 'Replace slip' : 'Upload slip' }}
                                    </button>
                                </div>

                                <!-- Slip Preview Box -->
                                <div v-if="newRepairForm.slipPreview" class="slip-preview-box">
                                    <div class="slip-thumb" @click="openLightbox(newRepairForm.slipPreview, 'Shipping Slip: ' + newRepairForm.slipName)">
                                        <span v-if="isGif(newRepairForm.slipName || newRepairForm.slipPreview)" class="gif-badge">GIF</span>
                                        <img :src="newRepairForm.slipPreview" alt="Shipping Slip Preview">
                                    </div>
                                    <div class="slip-preview-info">
                                        <strong>@{{ newRepairForm.slipName }}</strong>
                                        <small>Shipping document attached &middot; Click image to inspect full size</small>
                                    </div>
                                    <button type="button" class="button button-ghost" style="padding:5px 9px;font-size:10px;color:#dc2626;" @click="removeSlipFile">Remove</button>
                                </div>

                                <!-- Warning if Mail-in without slip -->
                                <div v-if="!newRepairForm.slipFile" class="mail-slip-alert">
                                    <svg class="icon" width="16" height="16"><use href="#icon-info"></use></svg>
                                    <span><strong>Slip required:</strong> When selecting Mail-in service, you must upload your shipping slip to proceed.</span>
                                </div>
                            </div>

                            <button type="submit" class="button button-primary" :disabled="!newRepairForm.serial || !newRepairForm.symptom || submittingRepair || (newRepairForm.delivery === 'mail' && !newRepairForm.slipFile)">
                                <svg class="icon" width="16" height="16"><use href="#icon-arrow"></use></svg>
                                @{{ submittingRepair ? 'Submitting & Dispatching Email...' : (repairSubmitted ? 'Request received' : 'Submit repair request') }}
                            </button>

                            <div v-if="repairSubmitted" class="submission-confirmation">
                                <span><svg class="icon" width="16" height="16"><use href="#icon-check"></use></svg></span>
                                <div>
                                    <strong>Your repair request is ready (#@{{ lastSubmittedRepairId }})</strong>
                                    <p>We've dispatched a confirmation email to @{{ currentUser.email }} with tracking instructions.</p>
                                </div>
                                <button type="button" class="button button-ghost" @click="navigateCustomer('repairs', lastSubmittedRepairId)">View my repairs</button>
                            </div>
                        </form>
                    </div>

                    <!-- My Repairs List -->
                    <div v-if="customerTab === 'repairs'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Repair tracking</p>
                                <h1>My repairs</h1>
                                <p>Select a repair to expand its progress and latest workshop update.</p>
                            </div>
                            <button class="button button-primary" @click="navigateCustomer('new-repair')">
                                <svg class="icon" width="16" height="16"><use href="#icon-plus"></use></svg>Start a repair
                            </button>
                        </div>

                        <section class="panel repair-list-panel">
                            <div class="repair-list-head">
                                <div>
                                    <span class="kicker">Repair history</span>
                                    <h2>Your devices</h2>
                                </div>
                                <span>@{{ customerRepairs.length }} repairs</span>
                            </div>

                            <div class="repair-accordion">
                                <article v-for="repair in customerRepairs" :key="repair.id" class="repair-list-item" :class="{ expanded: expandedCustomerRepairId === repair.id }">
                                    <button class="repair-list-trigger" @click="toggleCustomerRepair(repair.id)">
                                        <span class="device-icon"><svg class="icon" width="21" height="21"><use href="#icon-computer"></use></svg></span>
                                        <span class="repair-list-primary">
                                            <small>@{{ repair.id }} &middot; @{{ repair.created_date_formatted }}</small>
                                            <strong>@{{ repair.device }}</strong>
                                            <span>Serial: @{{ repair.serial }}</span>
                                        </span>
                                        <span class="repair-list-method">
                                            <svg class="icon" width="15" height="15"><use :href="repair.method === 'Mail-in' ? '#icon-mail' : '#icon-map'"></use></svg>
                                            @{{ repair.method }}
                                        </span>
                                        <span :class="'status-pill status-' + repair.status.toLowerCase().replaceAll(' ', '-')">
                                            <span class="status-dot"></span>@{{ repair.status }}
                                        </span>
                                        <span class="expand-icon"><svg class="icon" width="17" height="17"><use href="#icon-chevron"></use></svg></span>
                                    </button>

                                    <div v-if="expandedCustomerRepairId === repair.id" class="repair-list-details">
                                        <div class="timeline">
                                            <template v-for="(stage, sIndex) in getTimelineStages(repair)" :key="stage">
                                                <div class="timeline-step" :class="{ done: sIndex <= getRepairActiveStep(repair), current: sIndex === getRepairActiveStep(repair) && repair.status !== 'Finished' }">
                                                    <span class="timeline-node">
                                                        <svg v-if="sIndex <= getRepairActiveStep(repair)" class="icon" width="13" height="13"><use href="#icon-check"></use></svg>
                                                        <template v-else>@{{ sIndex + 1 }}</template>
                                                    </span>
                                                    <div>
                                                        <strong>@{{ stage }}</strong>
                                                        <small>@{{ sIndex < getRepairActiveStep(repair) ? 'Completed' : (sIndex === getRepairActiveStep(repair) ? (repair.status === 'Finished' ? 'Completed' : 'In progress') : 'Upcoming') }}</small>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="update-note">
                                            <svg class="icon" width="18" height="18"><use href="#icon-activity"></use></svg>
                                            <div>
                                                <strong>@{{ repair.status === 'Finished' ? 'Final update' : 'Latest workshop note' }}</strong>
                                                <p>@{{ repair.latest_update }}</p>
                                            </div>
                                            <span>@{{ repair.created_date_formatted }}</span>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </section>
                    </div>

                    <!-- Rate & Complain Form -->
                    <div v-if="customerTab === 'complaint'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Customer care</p>
                                <h1>Rate your repair or report a complaint</h1>
                                <p>Share a rating when things went well, or tell us when something needs attention.</p>
                            </div>
                        </div>

                        <form class="panel form-panel standalone-card" @submit.prevent="submitFeedback">
                            <div class="panel-head">
                                <div>
                                    <span class="kicker">Rate &amp; complain</span>
                                    <h2>What would you like to share?</h2>
                                    <p>Choose one option to send feedback about your repair experience.</p>
                                </div>
                                <span class="step-badge">
                                    <svg class="icon" width="16" height="16"><use :href="feedbackForm.mode === 'rating' ? '#icon-star' : '#icon-file'"></use></svg>
                                </span>
                            </div>

                            <div class="feedback-mode">
                                <button type="button" class="rating-option" :class="{ active: feedbackForm.mode === 'rating' }" @click="feedbackForm.mode = 'rating'; feedbackSubmitted = false">
                                    <span><svg class="icon" width="18" height="18"><use href="#icon-star"></use></svg></span>
                                    <div><strong>Rate the job</strong><p>Leave a star rating and optional feedback</p></div>
                                    <i><svg class="icon" width="13" height="13"><use href="#icon-check"></use></svg></i>
                                </button>
                                <button type="button" class="complaint-option" :class="{ active: feedbackForm.mode === 'complaint' }" @click="feedbackForm.mode = 'complaint'; feedbackSubmitted = false">
                                    <span><svg class="icon" width="18" height="18"><use href="#icon-file"></use></svg></span>
                                    <div><strong>Report a complaint</strong><p>Tell the workshop about a problem</p></div>
                                    <i><svg class="icon" width="13" height="13"><use href="#icon-check"></use></svg></i>
                                </button>
                            </div>

                            <label class="field">
                                <span>Related repair</span>
                                <select v-model="feedbackForm.repair_id">
                                    <option v-for="r in customerRepairs" :key="r.id" :value="r.id">@{{ r.id }} &middot; @{{ r.device }}</option>
                                    <option value="general">General complaint / feedback</option>
                                </select>
                            </label>

                            <!-- RATING MODE -->
                            <template v-if="feedbackForm.mode === 'rating'">
                                <div class="rating-field">
                                    <span>How would you rate this repair?</span>
                                    <div>
                                        <button type="button" v-for="n in 5" :key="n" :class="{ selected: n <= feedbackForm.rating }" @click="feedbackForm.rating = n">
                                            <svg class="icon" width="27" height="27"><use href="#icon-star"></use></svg>
                                        </button>
                                        <strong>@{{ feedbackForm.rating === 0 ? 'Choose a rating' : ['', 'Poor', 'Fair', 'Good', 'Very good', 'Excellent'][feedbackForm.rating] }}</strong>
                                    </div>
                                </div>

                                <label class="field">
                                    <span>Tell us more <small>Optional</small></span>
                                    <textarea v-model="feedbackForm.message" placeholder="What did the technician do well? What could improve?"></textarea>
                                </label>

                                <div class="form-actions">
                                    <p><svg class="icon" width="14" height="14"><use href="#icon-info"></use></svg>Your feedback helps improve our workshop.</p>
                                    <button type="submit" class="button button-primary" :disabled="feedbackForm.rating === 0 || submittingFeedback">
                                        <svg class="icon" width="16" height="16"><use href="#icon-star"></use></svg>
                                        @{{ submittingFeedback ? 'Submitting...' : 'Submit rating' }}
                                    </button>
                                </div>

                                <div v-if="feedbackSubmitted" class="submission-confirmation">
                                    <span><svg class="icon" width="16" height="16"><use href="#icon-check"></use></svg></span>
                                    <div>
                                        <strong>Thank you for your @{{ feedbackForm.rating }}-star rating!</strong>
                                        <p>Your feedback has been saved and an email alert was dispatched to the workshop manager.</p>
                                    </div>
                                </div>
                            </template>

                            <!-- COMPLAINT MODE -->
                            <template v-else>
                                <label class="field">
                                    <span>Complaint subject</span>
                                    <input v-model="feedbackForm.subject" placeholder="Briefly summarize the issue" required>
                                </label>

                                <label class="field">
                                    <span>What happened?</span>
                                    <textarea v-model="feedbackForm.message" placeholder="Describe your concern, what you expected, and how you would like us to help." required></textarea>
                                </label>

                                <div class="upload-zone" @click="$refs.complaintAttachment.click()">
                                    <input ref="complaintAttachment" type="file" accept="image/*,.pdf" @change="handleComplaintAttachment">
                                    <span><svg class="icon" width="18" height="18"><use :href="feedbackForm.attachmentName ? '#icon-check' : '#icon-upload'"></use></svg></span>
                                    <div>
                                        <strong>@{{ feedbackForm.attachmentName || 'Add supporting evidence' }}</strong>
                                        <p>@{{ feedbackForm.attachmentName ? 'Attachment ready to send' : 'Photo, screenshot, or PDF &middot; Optional' }}</p>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <p><svg class="icon" width="14" height="14"><use href="#icon-clock"></use></svg>We usually respond within one business day.</p>
                                    <button type="submit" class="button button-primary" :disabled="!feedbackForm.subject || !feedbackForm.message || submittingFeedback">
                                        <svg class="icon" width="16" height="16"><use href="#icon-arrow"></use></svg>
                                        @{{ submittingFeedback ? 'Submitting...' : 'Submit complaint' }}
                                    </button>
                                </div>

                                <div v-if="feedbackSubmitted" class="submission-confirmation">
                                    <span><svg class="icon" width="16" height="16"><use href="#icon-check"></use></svg></span>
                                    <div>
                                        <strong>We received your complaint</strong>
                                        <p>Reference @{{ lastFeedbackId }}. An email notification was sent to management and you will receive a response within 1 business day.</p>
                                    </div>
                                </div>
                            </template>
                        </form>
                    </div>

                    <!-- Profile Page -->
                    <div v-if="customerTab === 'profile'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Your account</p>
                                <h1>Profile &amp; contact details</h1>
                                <p>Keep your information current so the workshop can reach you regarding repair tickets.</p>
                            </div>
                        </div>

                        <section class="panel profile-panel standalone-card">
                            <div class="panel-head">
                                <div>
                                    <span class="kicker">Personal information</span>
                                    <h2>Your profile</h2>
                                </div>
                                <button class="text-button" @click="toggleEditProfile">
                                    <svg class="icon" width="15" height="15"><use :href="editingProfile ? '#icon-check' : '#icon-edit'"></use></svg>
                                    @{{ editingProfile ? 'Save changes' : 'Edit profile' }}
                                </button>
                            </div>

                            <div class="profile-identity">
                                <div class="large-avatar">@{{ currentUser.avatar_initials || 'MC' }}</div>
                                <div>
                                    <strong>@{{ currentUser.name }}</strong>
                                    <p>@{{ currentUser.bio_meta || 'Customer · Active' }}</p>
                                </div>
                            </div>

                            <div class="profile-fields">
                                <template v-if="editingProfile">
                                    <label class="field">
                                        <span>Full name</span>
                                        <input v-model="profileForm.name">
                                    </label>
                                    <label class="field">
                                        <span>Telephone number</span>
                                        <input v-model="profileForm.phone" type="tel">
                                    </label>
                                </template>
                                <template v-else>
                                    <div><span>Full name</span><strong>@{{ currentUser.name }}</strong></div>
                                    <div><span>Telephone number</span><strong>@{{ currentUser.phone || '+1 (202) 555-0182' }}</strong></div>
                                    <div><span>Email address</span><strong>@{{ currentUser.email }}</strong></div>
                                </template>
                            </div>

                            <div class="workshop-card">
                                <svg class="icon" width="20" height="20"><use href="#icon-map"></use></svg>
                                <div>
                                    <strong>Fixsmith Workshop</strong>
                                    <p>84 Mercer Street, Brooklyn, NY<br>Mon–Sat, 9:00 AM–6:00 PM</p>
                                </div>
                            </div>
                        </section>
                    </div>
                </template>

                <!-- ADMIN VIEW -->
                <template v-if="currentRole === 'admin'">
                    <!-- Dashboard -->
                    <div v-if="adminTab === 'dashboard'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Operations overview</p>
                                <h1>Workshop dashboard</h1>
                                <p>Monitor repair flow, team capacity, and workshop performance.</p>
                            </div>
                            <button class="button button-primary" @click="smtpModalOpen = true">
                                <svg class="icon" width="16" height="16"><use href="#icon-mail"></use></svg>SMTP Settings &amp; Test
                            </button>
                        </div>

                        <section class="metrics-grid">
                            <div class="metric-card">
                                <div class="metric-top">
                                    <span><svg class="icon" width="18" height="18"><use href="#icon-tool"></use></svg></span>
                                    <small>+8.2%</small>
                                </div>
                                <p>Active repairs</p>
                                <strong>@{{ activeRepairsCount }}</strong>
                                <small>Across 4 technicians</small>
                            </div>
                            <div class="metric-card">
                                <div class="metric-top">
                                    <span><svg class="icon" width="18" height="18"><use href="#icon-inbox"></use></svg></span>
                                    <small>+12.5%</small>
                                </div>
                                <p>New requests</p>
                                <strong>@{{ pendingRepairsCount }}</strong>
                                <small>Ready for assignment</small>
                            </div>
                            <div class="metric-card">
                                <div class="metric-top">
                                    <span><svg class="icon" width="18" height="18"><use href="#icon-box"></use></svg></span>
                                </div>
                                <p>Waiting for parts</p>
                                <strong>@{{ waitingPartsCount }}</strong>
                                <small>In workshop transit</small>
                            </div>
                            <div class="metric-card">
                                <div class="metric-top">
                                    <span><svg class="icon" width="18" height="18"><use href="#icon-check"></use></svg></span>
                                    <small>+16.4%</small>
                                </div>
                                <p>Completed this month</p>
                                <strong>@{{ finishedRepairsCount }}</strong>
                                <small>94% on-time completion</small>
                            </div>
                        </section>

                        <section class="admin-grid">
                            <div class="panel chart-panel">
                                <div class="panel-head">
                                    <div>
                                        <span class="kicker">Repair volume</span>
                                        <h2>Weekly activity</h2>
                                    </div>
                                    <button class="period-button">Last 7 days <svg class="icon" width="14" height="14"><use href="#icon-chevron"></use></svg></button>
                                </div>
                                <div class="chart">
                                    <div class="bar-group" v-for="(h, i) in [42, 63, 48, 82, 70, 92, 64]" :key="i">
                                        <div class="bar-track"><span :style="{ height: h + '%' }"></span></div>
                                        <small>@{{ ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][i] }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="panel workload-panel">
                                <div class="panel-head">
                                    <div>
                                        <span class="kicker">Team capacity</span>
                                        <h2>Technician workload</h2>
                                    </div>
                                </div>
                                <div class="workload-row" v-for="tech in technicianUsers" :key="tech.id">
                                    <div class="avatar small">@{{ tech.avatar_initials }}</div>
                                    <div>
                                        <span>
                                            <strong>@{{ tech.name }}</strong>
                                            <small>@{{ getTechAssignedCount(tech.name) }} active</small>
                                        </span>
                                        <div class="progress">
                                            <i :style="{ width: Math.min(100, (getTechAssignedCount(tech.name) / (tech.technician_capacity || 3)) * 100) + '%' }"></i>
                                        </div>
                                    </div>
                                </div>
                                <button class="button button-secondary" @click="navigateAdmin('team')">Manage team</button>
                            </div>
                        </section>
                    </div>

                    <!-- Requests Page -->
                    <div v-if="adminTab === 'requests'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Request management</p>
                                <h1>Customer repair requests</h1>
                                <p>Review incoming work, assign technicians, and monitor every request.</p>
                            </div>
                        </div>

                        <section class="panel requests-panel">
                            <div class="panel-head">
                                <div>
                                    <span class="kicker">Queue</span>
                                    <h2>All requests</h2>
                                </div>
                                <div class="search-box">
                                    <svg class="icon" width="16" height="16"><use href="#icon-search"></use></svg>
                                    <input v-model="requestSearchQuery" placeholder="Search requests...">
                                </div>
                            </div>

                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Request</th>
                                            <th>Device &amp; issue</th>
                                            <th>Delivery</th>
                                            <th>Status</th>
                                            <th>Technician</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="repair in paginatedRepairs" :key="repair.id">
                                            <tr :class="{ 'row-expanded': expandedAdminRepairId === repair.id }">
                                                <td>
                                                    <strong>@{{ repair.customer_name }}</strong>
                                                    <small>@{{ repair.id }} &middot; @{{ repair.created_date_formatted }}</small>
                                                </td>
                                                <td>
                                                    <strong>@{{ repair.device }}</strong>
                                                    <small>@{{ repair.issue }}</small>
                                                </td>
                                                <td>
                                                    <span class="method">
                                                        <svg class="icon" width="15" height="15"><use :href="repair.method === 'Mail-in' ? '#icon-mail' : '#icon-map'"></use></svg>
                                                        @{{ repair.method }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span :class="'status-pill status-' + repair.status.toLowerCase().replaceAll(' ', '-')">
                                                        <span class="status-dot"></span>@{{ repair.status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <select :value="repair.technician_name || 'Unassigned'"
                                                                :disabled="repair.method === 'Mail-in' && repair.status === 'Pending'"
                                                                @change="assignTechnicianToRepair(repair.id, $event.target.value)"
                                                                :title="repair.method === 'Mail-in' && repair.status === 'Pending' ? 'Locked: Awaiting package arrival & slip verification' : ''">
                                                            <option value="Unassigned">Unassigned</option>
                                                            <option v-for="t in technicianUsers" :key="t.id" :value="t.name">@{{ t.name }}</option>
                                                        </select>
                                                        <small v-if="repair.method === 'Mail-in' && repair.status === 'Pending'" style="display:block;color:#b45309;font-size:8px;margin-top:3px;font-weight:700;">
                                                            ⚠ Needs package receipt
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="row-action" @click="toggleAdminRepairDetail(repair.id)" :aria-label="'View details for ' + repair.id" :title="expandedAdminRepairId === repair.id ? 'Collapse details' : 'Expand details & images'">
                                                        <svg class="icon" width="14" height="14"><use :href="expandedAdminRepairId === repair.id ? '#icon-chevron' : '#icon-arrow'"></use></svg>
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Expanded Details Row -->
                                            <tr v-if="expandedAdminRepairId === repair.id" class="request-detail-row">
                                                <td colspan="6">
                                                    <div class="request-detail-content">
                                                        <!-- Customer Narrative & Info -->
                                                        <div class="detail-narrative">
                                                            <div class="detail-block">
                                                                <h4>Customer &amp; Ticket Information</h4>
                                                                <div class="detail-meta-list">
                                                                    <div><span>Client Name</span><strong>@{{ repair.customer_name }}</strong></div>
                                                                    <div><span>Client Email</span><strong>@{{ repair.customer_email }}</strong></div>
                                                                    <div><span>Hardware</span><strong>@{{ repair.device }}</strong></div>
                                                                    <div><span>Serial Number</span><strong>@{{ repair.serial }}</strong></div>
                                                                    <div><span>Delivery Method</span><strong>@{{ repair.method }}</strong></div>
                                                                    <div><span>Current Status</span><strong>@{{ repair.status }}</strong></div>
                                                                </div>
                                                            </div>

                                                            <div class="detail-block">
                                                                <h4>Reported Problem / Customer Narrative</h4>
                                                                <p>@{{ repair.issue }}</p>
                                                            </div>

                                                            <div class="detail-block">
                                                                <h4>Latest Workshop Update</h4>
                                                                <p>@{{ repair.latest_update || 'No updates logged yet.' }}</p>
                                                            </div>
                                                        </div>

                                                        <!-- Separated Uploaded Images -->
                                                        <div class="detail-images-section">
                                                            <!-- Category 1: Computer Symptom Photos -->
                                                            <div class="image-category">
                                                                <div class="image-category-head">
                                                                    <h3>
                                                                        <svg class="icon" width="16" height="16"><use href="#icon-tool"></use></svg>
                                                                        Computer Symptom Photos
                                                                    </h3>
                                                                    <span>@{{ getRepairPhotos(repair).length }} photos</span>
                                                                </div>
                                                                <div v-if="getRepairPhotos(repair).length > 0" class="image-gallery-grid">
                                                                    <div v-for="(photo, pIdx) in getRepairPhotos(repair)" :key="pIdx" class="gallery-thumbnail" @click="openLightbox(photo, repair.device + ' - Symptom Photo ' + (pIdx + 1))">
                                                                        <span v-if="isGif(photo)" class="gif-badge">GIF</span>
                                                                        <img :src="photo" :alt="'Symptom ' + (pIdx + 1)">
                                                                        <div class="zoom-indicator">
                                                                            <svg class="icon" width="16" height="16"><use href="#icon-search"></use></svg>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <p v-else style="margin:0;font-size:11px;color:var(--muted);font-style:italic;">
                                                                    No symptom photos were attached by the customer.
                                                                </p>
                                                            </div>

                                                            <!-- Category 2: Courier Shipping Slip (Mail-in ONLY) -->
                                                            <div v-if="repair.method === 'Mail-in'" class="image-category" style="border-left: 3px solid #2563eb;">
                                                                <div class="image-category-head">
                                                                    <h3>
                                                                        <svg class="icon" width="16" height="16"><use href="#icon-mail"></use></svg>
                                                                        Courier Shipping Slip
                                                                    </h3>
                                                                    <span :class="['slip-status-badge', repair.status === 'Pending' ? 'unverified' : 'verified']">
                                                                        @{{ repair.status === 'Pending' ? 'Awaiting Receipt' : 'Verified & Received' }}
                                                                    </span>
                                                                </div>

                                                                <div v-if="repair.shipping_slip" class="slip-inspection-card">
                                                                    <div class="slip-inspection-thumb" @click="openLightbox(formatImageUrl(repair.shipping_slip), repair.id + ' - Courier Shipping Slip')">
                                                                        <span v-if="isGif(repair.shipping_slip)" class="gif-badge">GIF</span>
                                                                        <img :src="formatImageUrl(repair.shipping_slip)" alt="Shipping Slip">
                                                                    </div>
                                                                    <div class="slip-inspection-actions">
                                                                        <div>
                                                                            <strong style="font-size:11px;display:block;">Customer Courier Slip Attached</strong>
                                                                            <small style="color:var(--muted);font-size:9px;">Click thumbnail to verify courier label and tracking code.</small>
                                                                        </div>

                                                                        <!-- Action button to confirm receipt if pending -->
                                                                        <div v-if="repair.status === 'Pending'" style="margin-top:4px;">
                                                                            <button type="button" class="btn-receive-package" :disabled="receivingPackageId === repair.id" @click="confirmPackageReceived(repair.id)">
                                                                                <svg class="icon" width="14" height="14"><use href="#icon-check"></use></svg>
                                                                                @{{ receivingPackageId === repair.id ? 'Processing...' : 'Check Delivery Slip & Confirm Package Received' }}
                                                                            </button>
                                                                            <small style="display:block;margin-top:4px;color:#991b1b;font-size:9px;">
                                                                                * Marks status as "Package received" and unlocks technician assignment.
                                                                            </small>
                                                                        </div>
                                                                        <div v-else style="display:flex;align-items:center;gap:6px;color:#166534;font-size:11px;font-weight:700;">
                                                                            <svg class="icon" width="14" height="14"><use href="#icon-check"></use></svg>
                                                                            Package checked in &middot; Technician assignment unlocked
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <p v-else style="margin:0;font-size:11px;color:#dc2626;">
                                                                    No shipping slip was found for this mail-in request.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="table-footer">
                                <div class="page-size">
                                    <span>Rows per page</span>
                                    <select v-model.number="requestPageSize">
                                        <option :value="2">2</option>
                                        <option :value="4">4</option>
                                        <option :value="10">10</option>
                                    </select>
                                </div>
                                <p>Showing @{{ filteredRepairs.length === 0 ? 0 : (requestPage - 1) * requestPageSize + 1 }}–@{{ Math.min(requestPage * requestPageSize, filteredRepairs.length) }} of @{{ filteredRepairs.length }} requests</p>
                                <div>
                                    <button class="button button-secondary" :disabled="requestPage === 1" @click="requestPage--">Previous</button>
                                    <span class="page-number">@{{ requestPage }} / @{{ requestMaxPage }}</span>
                                    <button class="button button-secondary" :disabled="requestPage >= requestMaxPage" @click="requestPage++">Next</button>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Accounts Directory -->
                    <div v-if="adminTab === 'accounts'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Account directory</p>
                                <h1>All user accounts</h1>
                                <p>Manage customers, technicians, and workshop administrators.</p>
                            </div>
                            <button class="button button-primary" @click="showAddAccountForm = !showAddAccountForm">
                                <svg class="icon" width="16" height="16"><use :href="showAddAccountForm ? '#icon-chevron' : '#icon-plus'"></use></svg>
                                @{{ showAddAccountForm ? 'Close form' : 'Add account' }}
                            </button>
                        </div>

                        <!-- Add User Form -->
                        <form v-if="showAddAccountForm" class="panel account-form" @submit.prevent="submitNewAccount">
                            <div class="panel-head">
                                <div>
                                    <span class="kicker">New user</span>
                                    <h2>Create an account</h2>
                                    <p>Choose the correct access role before creating the account.</p>
                                </div>
                            </div>
                            <div class="account-form-grid">
                                <label class="field">
                                    <span>Full name</span>
                                    <input v-model="newAccountForm.name" placeholder="Enter full name" required>
                                </label>
                                <label class="field">
                                    <span>Email address</span>
                                    <input type="email" v-model="newAccountForm.email" placeholder="name@example.com" required>
                                </label>
                                <label class="field">
                                    <span>Temporary password</span>
                                    <input type="password" v-model="newAccountForm.password" placeholder="At least 8 characters" required>
                                </label>
                                <label class="field">
                                    <span>Account role</span>
                                    <select v-model="newAccountForm.role">
                                        <option value="Customer">Customer</option>
                                        <option value="Technician">Technician</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </label>
                            </div>
                            <div class="account-form-actions">
                                <p><svg class="icon" width="14" height="14"><use href="#icon-info"></use></svg>The user can change their temporary password after signing in.</p>
                                <button type="submit" class="button button-primary" :disabled="!newAccountForm.name || !newAccountForm.email || newAccountForm.password.length < 8">
                                    <svg class="icon" width="16" height="16"><use href="#icon-plus"></use></svg>Create account
                                </button>
                            </div>
                        </form>

                        <div class="account-catalogs">
                            <!-- Customers -->
                            <section class="panel account-catalog">
                                <div class="panel-head">
                                    <div><span class="kicker">Customers</span><h2>Customer accounts</h2></div>
                                    <span class="count-badge">@{{ customerUsers.length }}</span>
                                </div>
                                <div v-for="user in customerUsers" :key="user.id" class="account-row" :class="{ inactive: !user.active }">
                                    <div class="avatar">@{{ user.avatar_initials || 'CU' }}</div>
                                    <div>
                                        <strong>@{{ user.name }}</strong>
                                        <small>@{{ user.email }}</small>
                                    </div>
                                    <span class="account-state" :class="{ inactive: !user.active }">
                                        <i></i>@{{ user.active ? (user.bio_meta || 'Active') : 'Deactivated' }}
                                    </span>
                                    <button class="row-action" @click="openEditAccountModal(user)">
                                        <svg class="icon" width="14" height="14"><use href="#icon-edit"></use></svg>
                                    </button>
                                </div>
                            </section>

                            <!-- Technicians -->
                            <section class="panel account-catalog">
                                <div class="panel-head">
                                    <div><span class="kicker">Workshop team</span><h2>Technician accounts</h2></div>
                                    <span class="count-badge">@{{ technicianUsers.length }}</span>
                                </div>
                                <div v-for="user in technicianUsers" :key="user.id" class="account-row" :class="{ inactive: !user.active }">
                                    <div class="avatar">@{{ user.avatar_initials || 'TK' }}</div>
                                    <div>
                                        <strong>@{{ user.name }}</strong>
                                        <small>@{{ user.technician_title || user.email }}</small>
                                    </div>
                                    <span class="account-state" :class="{ inactive: !user.active }">
                                        <i></i>@{{ user.active ? (getTechAssignedCount(user.name) + ' active') : 'Deactivated' }}
                                    </span>
                                    <button class="row-action" @click="openEditAccountModal(user)">
                                        <svg class="icon" width="14" height="14"><use href="#icon-edit"></use></svg>
                                    </button>
                                </div>
                            </section>

                            <!-- Admins -->
                            <section class="panel account-catalog">
                                <div class="panel-head">
                                    <div><span class="kicker">Administration</span><h2>Admin accounts</h2></div>
                                    <span class="count-badge">@{{ adminUsers.length }}</span>
                                </div>
                                <div v-for="user in adminUsers" :key="user.id" class="account-row" :class="{ inactive: !user.active }">
                                    <div class="avatar">@{{ user.avatar_initials || 'AD' }}</div>
                                    <div>
                                        <strong>@{{ user.name }}</strong>
                                        <small>@{{ user.email }}</small>
                                    </div>
                                    <span class="account-state" :class="{ inactive: !user.active }">
                                        <i></i>@{{ user.active ? (user.bio_meta || 'Administrator') : 'Deactivated' }}
                                    </span>
                                    <button class="row-action" @click="openEditAccountModal(user)">
                                        <svg class="icon" width="14" height="14"><use href="#icon-edit"></use></svg>
                                    </button>
                                </div>
                            </section>
                        </div>

                        <!-- Edit Account Modal -->
                        <div v-if="editingUser" class="modal-backdrop" @mousedown="editingUser = null">
                            <section class="account-modal" @mousedown.stop>
                                <div class="modal-head">
                                    <div>
                                        <span class="kicker">Account management</span>
                                        <h2>Edit account</h2>
                                        <p>Update login details or control access to this account.</p>
                                    </div>
                                    <button class="modal-close" @click="editingUser = null">&times;</button>
                                </div>
                                <form @submit.prevent="saveAccountEdit">
                                    <div class="modal-account-summary">
                                        <div class="large-avatar">@{{ editingUser.avatar_initials }}</div>
                                        <div>
                                            <strong>@{{ editAccountForm.name }}</strong>
                                            <p>@{{ editingUser.role }} account &middot; @{{ editingUser.email }}</p>
                                        </div>
                                        <span class="account-state" :class="{ inactive: !editAccountForm.active }">
                                            <i></i>@{{ editAccountForm.active ? 'Active' : 'Deactivated' }}
                                        </span>
                                    </div>
                                    <div class="modal-fields">
                                        <label class="field">
                                            <span>Account name</span>
                                            <input v-model="editAccountForm.name" placeholder="Enter account name" required>
                                        </label>
                                        <label class="field">
                                            <span>New password</span>
                                            <input type="password" v-model="editAccountForm.newPassword" placeholder="Leave blank to keep current password">
                                        </label>
                                        <small>Password must contain at least 8 characters. Leave empty if password should not change.</small>
                                    </div>
                                    <div class="access-control" :class="{ deactivated: !editAccountForm.active }">
                                        <span>
                                            <svg class="icon" width="18" height="18"><use :href="editAccountForm.active ? '#icon-check' : '#icon-info'"></use></svg>
                                            <div>
                                                <strong>Account access</strong>
                                                <p>@{{ editAccountForm.active ? 'This user can currently sign in.' : 'This user is blocked from signing in.' }}</p>
                                            </div>
                                        </span>
                                        <button type="button" class="button button-secondary" @click="editAccountForm.active = !editAccountForm.active">
                                            @{{ editAccountForm.active ? 'Deactivate account' : 'Activate account' }}
                                        </button>
                                    </div>
                                    <div class="modal-actions">
                                        <button type="button" class="button button-secondary" @click="editingUser = null">Cancel</button>
                                        <button type="submit" class="button button-primary">
                                            <svg class="icon" width="16" height="16"><use href="#icon-check"></use></svg>Save changes
                                        </button>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </div>

                    <!-- Manage Team Board -->
                    <div v-if="adminTab === 'team'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Workshop staffing</p>
                                <h1>Manage team workload</h1>
                                <p>Drag untouched jobs between the unassigned queue and available technicians, or use the quick dropdown.</p>
                            </div>
                        </div>

                        <div class="drag-guidance">
                            <svg class="icon" width="18" height="18"><use href="#icon-info"></use></svg>
                            <p><strong>How reassignment works:</strong> Unassigned jobs and jobs marked “Not started” can be moved. Work already started is locked to its technician.</p>
                        </div>

                        <section class="team-board">
                            <!-- Unassigned Lane -->
                            <aside class="panel unassigned-lane"
                                   :class="{ 'drop-active': dropTargetLane === 'unassigned' }"
                                   @dragover.prevent="dropTargetLane = 'unassigned'"
                                   @dragleave.self="dropTargetLane = null"
                                   @drop.prevent="handleJobDrop('unassigned')">
                                <div class="team-lane-head">
                                    <div><span class="kicker">Dispatch queue</span><h2>Unassigned jobs</h2></div>
                                    <span>@{{ unassignedRepairs.length }}</span>
                                </div>
                                <div class="team-job-list">
                                    <article v-for="job in unassignedRepairs" :key="job.id" class="team-job"
                                             :draggable="!job.interacted && !(job.method === 'Mail-in' && job.status === 'Pending')"
                                             :class="{ locked: job.interacted || (job.method === 'Mail-in' && job.status === 'Pending'), draggable: !job.interacted && !(job.method === 'Mail-in' && job.status === 'Pending'), dragging: draggedJobId === job.id }"
                                             @dragstart="handleJobDragStart($event, job)"
                                             @dragend="handleJobDragEnd">
                                        <span class="drag-handle"><svg class="icon" width="16" height="16"><use :href="job.interacted ? '#icon-check' : (job.method === 'Mail-in' && job.status === 'Pending' ? '#icon-lock' : '#icon-menu')"></use></svg></span>
                                        <div>
                                            <span>
                                                <small>@{{ job.id }} &middot; @{{ job.method }}</small>
                                                <span :class="'status-pill status-' + job.status.toLowerCase().replaceAll(' ', '-')">@{{ job.status }}</span>
                                            </span>
                                            <strong>@{{ job.device }}</strong>
                                            <p>@{{ job.issue }}</p>
                                            <small>@{{ job.customer_name }}</small>
                                            <select class="team-job-reassign" :disabled="job.method === 'Mail-in' && job.status === 'Pending'" @change="reassignTeamJob(job.id, $event.target.value)">
                                                <option v-if="job.method === 'Mail-in' && job.status === 'Pending'" value="" selected disabled>⚠ Awaiting package arrival...</option>
                                                <option v-else value="" selected>Assign to technician...</option>
                                                <option v-for="ot in teamTechnicians" :key="ot.id" :value="ot.id">@{{ ot.name }}</option>
                                            </select>
                                        </div>
                                        <span class="interaction-state" :class="{ started: job.interacted, awaiting: job.method === 'Mail-in' && job.status === 'Pending' }">
                                            @{{ job.interacted ? 'Work started' : (job.method === 'Mail-in' && job.status === 'Pending' ? 'Awaiting package' : 'Not started') }}
                                        </span>
                                    </article>

                                    <div v-if="unassignedRepairs.length === 0" class="empty-drop"
                                         @dragover.prevent="dropTargetLane = 'unassigned'"
                                         @drop.prevent="handleJobDrop('unassigned')">
                                        <svg class="icon" width="20" height="20"><use href="#icon-check"></use></svg>
                                        <strong>Queue is clear</strong>
                                        <p>All active tickets have an assigned technician.</p>
                                    </div>
                                </div>
                            </aside>

                            <!-- Technician Lanes -->
                            <div class="technician-lanes">
                                <section v-for="tech in teamTechnicians" :key="tech.id" class="panel technician-lane"
                                         :class="{ idle: getJobsForTech(tech.name).length === 0, 'drop-active': dropTargetLane === tech.id }"
                                         @dragover.prevent="dropTargetLane = tech.id"
                                         @dragleave.self="dropTargetLane = null"
                                         @drop.prevent="handleJobDrop(tech.id)">
                                    <div class="technician-lane-head">
                                        <div class="avatar">@{{ tech.initials }}</div>
                                        <div>
                                            <span>
                                                <strong>@{{ tech.name }}</strong>
                                                <small v-if="getJobsForTech(tech.name).length === 0" class="idle-badge">Idle</small>
                                            </span>
                                            <p>@{{ tech.title }}</p>
                                        </div>
                                        <span>@{{ getJobsForTech(tech.name).length }}/@{{ tech.capacity }}</span>
                                    </div>

                                    <div v-if="getJobsForTech(tech.name).length > 0" class="team-job-list">
                                        <article v-for="job in getJobsForTech(tech.name)" :key="job.id" class="team-job"
                                                 :draggable="!job.interacted"
                                                 :class="{ locked: job.interacted, draggable: !job.interacted, dragging: draggedJobId === job.id }"
                                                 @dragstart="handleJobDragStart($event, job)"
                                                 @dragend="handleJobDragEnd">
                                            <span class="drag-handle"><svg class="icon" width="16" height="16"><use :href="job.interacted ? '#icon-check' : '#icon-menu'"></use></svg></span>
                                            <div>
                                                <span>
                                                    <small>@{{ job.id }}</small>
                                                    <span :class="'status-pill status-' + job.status.toLowerCase().replaceAll(' ', '-')">@{{ job.status }}</span>
                                                </span>
                                                <strong>@{{ job.device }}</strong>
                                                <p>@{{ job.issue }}</p>
                                                <small>@{{ job.customer_name }}</small>
                                                <select v-if="!job.interacted" class="team-job-reassign" @change="reassignTeamJob(job.id, $event.target.value)">
                                                    <option value="unassigned">Unassign job</option>
                                                    <option v-for="ot in teamTechnicians" :key="ot.id" :value="ot.id" :selected="ot.name === tech.name">@{{ ot.name }}</option>
                                                </select>
                                            </div>
                                            <span class="interaction-state" :class="{ started: job.interacted }">
                                                @{{ job.interacted ? 'Work started' : 'Not started' }}
                                            </span>
                                        </article>
                                    </div>
                                    <div v-else class="idle-dropzone"
                                         @dragover.prevent="dropTargetLane = tech.id"
                                         @drop.prevent="handleJobDrop(tech.id)">
                                        <svg class="icon" width="20" height="20"><use href="#icon-plus"></use></svg>
                                        <strong>Available for work</strong>
                                        <p>Assign a job from the unassigned queue or drop here.</p>
                                    </div>
                                </section>
                            </div>
                        </section>
                    </div>

                    <!-- Customer Feedback & Complaints Center -->
                    <div v-if="adminTab === 'complaints'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Customer care</p>
                                <h1>Ratings &amp; complaints</h1>
                                <p>Review star ratings and respond to complaints submitted from the customer feedback page.</p>
                            </div>
                        </div>

                        <section class="complaint-layout">
                            <div class="panel complaint-inbox">
                                <div class="panel-head">
                                    <div><span class="kicker">Feedback inbox</span><h2>Customer submissions</h2></div>
                                    <span class="nav-count">@{{ feedbackList.length }}</span>
                                </div>
                                <button v-for="fb in feedbackList" :key="fb.id" class="complaint-row" :class="['feedback-' + fb.type.toLowerCase(), { selected: selectedFeedback && selectedFeedback.id === fb.id }]" @click="selectedFeedback = fb">
                                    <span class="avatar" :class="fb.type === 'Rating' ? 'rating-avatar' : 'complaint-avatar'">
                                        <svg class="icon" width="16" height="16"><use :href="fb.type === 'Rating' ? '#icon-star' : '#icon-file'"></use></svg>
                                    </span>
                                    <span>
                                        <small>@{{ fb.type }} &middot; @{{ fb.id }} &middot; @{{ fb.repair_id }}</small>
                                        <strong>@{{ fb.subject }}</strong>
                                        <p>@{{ fb.message }}</p>
                                    </span>
                                    <span>
                                        <span v-if="fb.rating > 0" class="compact-stars">
                                            <span v-for="s in 5" :key="s" :class="{ filled: s <= fb.rating }">
                                                <svg class="icon" width="11" height="11"><use href="#icon-star"></use></svg>
                                            </span>
                                        </span>
                                        <span v-else :class="'status-pill status-' + fb.status.toLowerCase().replaceAll(' ', '-')">@{{ fb.status }}</span>
                                        <small>@{{ fb.age_formatted }}</small>
                                    </span>
                                </button>
                            </div>

                            <div v-if="selectedFeedback" class="panel complaint-detail">
                                <div class="panel-head">
                                    <div>
                                        <span class="kicker">@{{ selectedFeedback.type }} &middot; @{{ selectedFeedback.id }} &middot; Repair @{{ selectedFeedback.repair_id }}</span>
                                        <h2>@{{ selectedFeedback.subject }}</h2>
                                    </div>
                                    <div v-if="selectedFeedback.rating > 0" class="large-rating">
                                        <svg class="icon" width="16" height="16"><use href="#icon-star"></use></svg>@{{ selectedFeedback.rating }}.0
                                    </div>
                                    <span v-else :class="'status-pill status-' + selectedFeedback.status.toLowerCase().replaceAll(' ', '-')">@{{ selectedFeedback.status }}</span>
                                </div>

                                <div class="complainant">
                                    <span class="avatar">@{{ selectedFeedback.customer_name.split(' ').map(w=>w[0]).join('').slice(0,2) }}</span>
                                    <div>
                                        <strong>@{{ selectedFeedback.customer_name }}</strong>
                                        <small>@{{ selectedFeedback.type }} submission &middot; @{{ selectedFeedback.customer_email }}</small>
                                    </div>
                                </div>

                                <div class="complaint-message" :class="selectedFeedback.type === 'Rating' ? 'rating-message' : 'complaint-message-alert'">
                                    <svg class="icon" width="18" height="18"><use :href="selectedFeedback.type === 'Rating' ? '#icon-star' : '#icon-file'"></use></svg>
                                    <p>@{{ selectedFeedback.message }}</p>
                                </div>

                                <div v-if="selectedFeedback.admin_reply" style="margin-bottom: 16px; padding: 12px; background: #eaf3ed; border-radius: 9px; font-size: 11px;">
                                    <strong style="color: var(--forest);">Sent response:</strong>
                                    <p style="margin: 4px 0 0;">@{{ selectedFeedback.admin_reply }}</p>
                                </div>

                                <label class="field">
                                    <span>@{{ selectedFeedback.type === 'Rating' ? 'Thank the customer' : 'Reply to customer (Dispatches Email via SMTP)' }}</span>
                                    <textarea v-model="complaintReplyText" :placeholder="'Write a response to ' + selectedFeedback.customer_name + '...'"></textarea>
                                </label>

                                <div class="complaint-actions">
                                    <button class="button button-secondary" @click="markComplaintStatus(selectedFeedback.type === 'Rating' ? 'Reviewed' : 'Resolved')">
                                        <svg class="icon" width="14" height="14"><use href="#icon-check"></use></svg>
                                        @{{ selectedFeedback.type === 'Rating' ? 'Mark reviewed' : 'Mark resolved' }}
                                    </button>
                                    <button class="button button-primary" :disabled="!complaintReplyText || sendingComplaintReply" @click="sendComplaintReply">
                                        <svg class="icon" width="14" height="14"><use href="#icon-arrow"></use></svg>
                                        @{{ sendingComplaintReply ? 'Sending Email...' : 'Send response' }}
                                    </button>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Staff Profile Page -->
                    <div v-if="adminTab === 'profile'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Administrator account</p>
                                <h1>Profile &amp; contact details</h1>
                                <p>Keep your workshop identity and contact information up to date.</p>
                            </div>
                        </div>

                        <section class="panel profile-panel standalone-card">
                            <div class="panel-head">
                                <div><span class="kicker">Personal information</span><h2>Your profile</h2></div>
                                <button class="text-button" @click="toggleEditProfile">
                                    <svg class="icon" width="15" height="15"><use :href="editingProfile ? '#icon-check' : '#icon-edit'"></use></svg>
                                    @{{ editingProfile ? 'Save changes' : 'Edit profile' }}
                                </button>
                            </div>
                            <div class="profile-identity">
                                <div class="large-avatar">@{{ currentUser.avatar_initials || 'EW' }}</div>
                                <div><strong>@{{ currentUser.name }}</strong><p>Workshop administrator &middot; Active account</p></div>
                            </div>
                            <div class="profile-fields">
                                <template v-if="editingProfile">
                                    <label class="field"><span>Full name</span><input v-model="profileForm.name"></label>
                                    <label class="field"><span>Email address</span><input v-model="profileForm.email" type="email"></label>
                                    <label class="field"><span>Telephone number</span><input v-model="profileForm.phone" type="tel"></label>
                                </template>
                                <template v-else>
                                    <div><span>Full name</span><strong>@{{ currentUser.name }}</strong></div>
                                    <div><span>Email address</span><strong>@{{ currentUser.email }}</strong></div>
                                    <div><span>Telephone number</span><strong>@{{ currentUser.phone || '+1 (202) 555-0114' }}</strong></div>
                                    <div><span>Role</span><strong>Administrator</strong></div>
                                </template>
                            </div>
                            <div class="workshop-card">
                                <svg class="icon" width="20" height="20"><use href="#icon-settings"></use></svg>
                                <div>
                                    <strong>Administrative access</strong>
                                    <p>Full workshop, user account, email SMTP server, and repair dispatch permissions.</p>
                                </div>
                            </div>
                        </section>
                    </div>
                </template>

                <!-- TECHNICIAN VIEW -->
                <template v-if="currentRole === 'technician'">
                    <!-- Workspace Overview -->
                    <div v-if="technicianTab === 'workspace'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Technician workspace</p>
                                <h1>Good morning, @{{ currentUser.name.split(' ')[0] }}</h1>
                                <p>Focus on unfinished repairs currently assigned to you.</p>
                            </div>
                            <div class="date-chip"><svg class="icon" width="15" height="15"><use href="#icon-calendar"></use></svg>Active Workshop Queue</div>
                        </div>

                        <section class="summary-grid tech-summary">
                            <div class="summary-card featured">
                                <span class="summary-icon"><svg class="icon" width="20" height="20"><use href="#icon-tool"></use></svg></span>
                                <div>
                                    <p>Unfinished work</p>
                                    <strong>@{{ technicianUnfinishedCount }} repairs</strong>
                                    <small>Need technician action</small>
                                </div>
                            </div>
                            <div class="summary-card">
                                <span class="summary-icon orange"><svg class="icon" width="20" height="20"><use href="#icon-box"></use></svg></span>
                                <div>
                                    <p>Waiting for parts</p>
                                    <strong>@{{ technicianWaitingPartsCount }} repairs</strong>
                                    <small>Parts on order</small>
                                </div>
                            </div>
                            <div class="summary-card">
                                <span class="summary-icon green"><svg class="icon" width="20" height="20"><use href="#icon-check"></use></svg></span>
                                <div>
                                    <p>Completed this week</p>
                                    <strong>@{{ technicianCompletedCount }} repairs</strong>
                                    <small>Avg. turnaround 2.4 days</small>
                                </div>
                            </div>
                        </section>

                        <!-- Technician Carousel -->
                        <section v-if="technicianAssignedRepairs.length > 0" class="panel tracking-panel carousel-panel technician-carousel">
                            <div class="carousel-toolbar">
                                <div>
                                    <span class="kicker">Unfinished work</span>
                                    <span class="carousel-count">@{{ techCarouselIndex + 1 }} of @{{ technicianAssignedRepairs.length }}</span>
                                </div>
                                <div class="carousel-controls">
                                    <button class="carousel-button previous" @click="prevTechCarousel" aria-label="Previous assignment">
                                        <svg class="icon" width="16" height="16"><use href="#icon-arrow"></use></svg>
                                    </button>
                                    <button class="carousel-button" @click="nextTechCarousel" aria-label="Next assignment">
                                        <svg class="icon" width="16" height="16"><use href="#icon-arrow"></use></svg>
                                    </button>
                                </div>
                            </div>

                            <div v-if="activeTechCarouselRepair" class="carousel-content" :key="activeTechCarouselRepair.id">
                                <div class="workspace-head">
                                    <div>
                                        <span class="kicker">@{{ activeTechCarouselRepair.id }} &middot; @{{ activeTechCarouselRepair.method }}</span>
                                        <h2>@{{ activeTechCarouselRepair.device }}</h2>
                                        <p>@{{ activeTechCarouselRepair.customer_name }} &middot; Serial @{{ activeTechCarouselRepair.serial }}</p>
                                    </div>
                                    <span :class="'status-pill status-' + activeTechCarouselRepair.status.toLowerCase().replaceAll(' ', '-')">
                                        <span class="status-dot"></span>@{{ activeTechCarouselRepair.status }}
                                    </span>
                                </div>
                                <div class="technician-carousel-body">
                                    <div class="issue-card">
                                        <span><svg class="icon" width="18" height="18"><use href="#icon-info"></use></svg></span>
                                        <div>
                                            <strong>Reported issue</strong>
                                            <p>@{{ activeTechCarouselRepair.issue }}. This repair still requires technician action.</p>
                                        </div>
                                    </div>
                                    <div class="assignment-meta">
                                        <div><span>Customer</span><strong>@{{ activeTechCarouselRepair.customer_name }}</strong></div>
                                        <div><span>Received</span><strong>@{{ activeTechCarouselRepair.created_date_formatted }}</strong></div>
                                        <div><span>Service</span><strong>@{{ activeTechCarouselRepair.method }}</strong></div>
                                    </div>
                                    <div class="open-assignment">
                                        <p><svg class="icon" width="14" height="14"><use href="#icon-clock"></use></svg>Open this assignment to update its status and customer note.</p>
                                        <button class="button button-primary" @click="openAssignedRepairDetail(activeTechCarouselRepair.id)">
                                            <svg class="icon" width="14" height="14"><use href="#icon-arrow"></use></svg>Open assigned repair
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="carousel-footer">
                                <button class="autoplay-button" @click="techCarouselPaused = !techCarouselPaused">
                                    <svg class="icon" width="14" height="14"><use :href="techCarouselPaused ? '#icon-play' : '#icon-pause'"></use></svg>
                                    @{{ techCarouselPaused ? 'Resume auto-play' : 'Stop auto-play' }}
                                </button>
                                <div class="carousel-dots" :class="{ paused: techCarouselPaused }">
                                    <button v-for="(item, tIndex) in technicianAssignedRepairs" :key="item.id" :class="{ active: tIndex === techCarouselIndex }" @click="techCarouselIndex = tIndex">
                                        <span></span>
                                    </button>
                                </div>
                                <span class="carousel-footer-spacer"></span>
                            </div>
                        </section>
                    </div>

                    <!-- Assigned Repairs Queue -->
                    <div v-if="technicianTab === 'assigned'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Technician queue</p>
                                <h1>Assigned repairs</h1>
                                <p>Select a repair to update its status and customer-facing note. Changes will send email updates to the client.</p>
                            </div>
                        </div>

                        <section class="panel assigned-repair-list">
                            <div class="repair-list-head">
                                <div><span class="kicker">My assignments</span><h2>Active repair queue</h2></div>
                                <span>@{{ technicianAssignedRepairs.length }} repairs</span>
                            </div>

                            <article v-for="(repair, index) in technicianAssignedRepairs" :key="repair.id" class="technician-repair-item" :class="{ expanded: expandedTechRepairId === repair.id }">
                                <button class="technician-repair-trigger" @click="toggleTechRepair(repair.id)">
                                    <span class="job-order">@{{ String(index + 1).padStart(2, '0') }}</span>
                                    <span class="repair-list-primary">
                                        <small>@{{ repair.id }} &middot; @{{ repair.created_date_formatted }}</small>
                                        <strong>@{{ repair.device }}</strong>
                                        <span><svg class="icon" width="13" height="13"><use href="#icon-profile"></use></svg>@{{ repair.customer_name }} &middot; @{{ repair.method }}</span>
                                    </span>
                                    <span class="repair-issue-preview">@{{ repair.issue }}</span>
                                    <span :class="'status-pill status-' + (techStatusEdits[repair.id] || repair.status).toLowerCase().replaceAll(' ', '-')">
                                        <span class="status-dot"></span>@{{ techStatusEdits[repair.id] || repair.status }}
                                    </span>
                                    <span class="expand-icon"><svg class="icon" width="16" height="16"><use href="#icon-chevron"></use></svg></span>
                                </button>

                                <div v-if="expandedTechRepairId === repair.id" class="technician-update-form">
                                    <div class="issue-card">
                                        <span><svg class="icon" width="18" height="18"><use href="#icon-info"></use></svg></span>
                                        <div>
                                            <strong>Reported issue</strong>
                                            <p>@{{ repair.issue }}. Review the customer’s description before recording your workshop update.</p>
                                        </div>
                                    </div>

                                    <!-- Customer Uploaded Photos & Files for Technician (Delivery slip excluded) -->
                                    <div v-if="getRepairPhotos(repair).length > 0" class="technician-images-box">
                                        <h4>
                                            <svg class="icon" width="16" height="16"><use href="#icon-file"></use></svg>
                                            Customer Uploaded Symptom Photos &amp; Files (@{{ getRepairPhotos(repair).length }})
                                        </h4>
                                        <div class="image-gallery-grid">
                                            <div v-for="(img, pIdx) in getRepairPhotos(repair)" :key="pIdx" class="gallery-thumbnail" @click="openLightbox(img, repair.device + ' - Photo ' + (pIdx + 1))">
                                                <span v-if="isGif(img)" class="gif-badge">GIF</span>
                                                <img :src="img" :alt="'Photo ' + (pIdx + 1)">
                                                <div class="zoom-indicator"><svg class="icon" width="16" height="16"><use href="#icon-search"></use></svg></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="status-editor">
                                        <div class="section-label">
                                            <span>Update repair status</span>
                                            <small>Dispatches notification email to @{{ repair.customer_email }} immediately</small>
                                        </div>
                                        <div class="status-options">
                                            <button v-for="st in allStatuses" :key="st" :class="{ active: (techStatusEdits[repair.id] || repair.status) === st }" @click="setTechStatus(repair.id, st)">
                                                <span><svg v-if="(techStatusEdits[repair.id] || repair.status) === st" class="icon" width="13" height="13"><use href="#icon-check"></use></svg></span>
                                                @{{ st }}
                                            </button>
                                        </div>
                                    </div>

                                    <label class="field">
                                        <span>Customer update note</span>
                                        <textarea v-model="techNoteEdits[repair.id]" placeholder="Explain what you found, what work was completed, or what happens next..."></textarea>
                                    </label>

                                    <div class="workspace-actions">
                                        <span><svg class="icon" width="14" height="14"><use href="#icon-clock"></use></svg>Updated in real-time</span>
                                        <button class="button button-primary" :disabled="savingTechRepair === repair.id" @click="saveTechRepairUpdate(repair)">
                                            <svg class="icon" width="14" height="14"><use href="#icon-check"></use></svg>
                                            @{{ savingTechRepair === repair.id ? 'Saving & Sending Email...' : (savedTechRepairId === repair.id ? 'Update saved!' : 'Save update') }}
                                        </button>
                                    </div>
                                </div>
                            </article>
                        </section>
                    </div>

                    <!-- Technician Complaints / Feedback -->
                    <div v-if="technicianTab === 'complaints'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Customer care</p>
                                <h1>Feedback on your repairs</h1>
                                <p>Review ratings and complaints connected only to cases assigned to you.</p>
                            </div>
                        </div>

                        <section class="panel technician-complaints">
                            <div class="repair-list-head">
                                <div><span class="kicker">Customer submissions</span><h2>Ratings &amp; complaints</h2></div>
                                <span>@{{ technicianFeedbackList.length }} items</span>
                            </div>

                            <article v-for="fb in technicianFeedbackList" :key="fb.id" class="technician-complaint" :class="'feedback-' + fb.type.toLowerCase()">
                                <div class="avatar" :class="fb.type === 'Rating' ? 'rating-avatar' : 'complaint-avatar'">
                                    <svg class="icon" width="16" height="16"><use :href="fb.type === 'Rating' ? '#icon-star' : '#icon-file'"></use></svg>
                                </div>
                                <div>
                                    <span>
                                        <small>@{{ fb.type }} &middot; @{{ fb.id }} &middot; Repair @{{ fb.repair_id }}</small>
                                        <span v-if="fb.rating > 0" class="large-rating small"><svg class="icon" width="13" height="13"><use href="#icon-star"></use></svg>@{{ fb.rating }}.0</span>
                                        <span v-else :class="'status-pill status-' + fb.status.toLowerCase().replaceAll(' ', '-')">@{{ fb.status }}</span>
                                    </span>
                                    <strong>@{{ fb.subject }}</strong>
                                    <p>@{{ fb.message }}</p>
                                    <small>From @{{ fb.customer_name }} &middot; @{{ fb.age_formatted }}</small>
                                </div>
                                <button class="button button-secondary" @click="showToast('Reviewing ' + fb.id, 'success')">
                                    @{{ fb.type === 'Rating' ? 'View feedback' : 'View case' }}
                                </button>
                            </article>
                        </section>
                    </div>

                    <!-- Staff Profile Page -->
                    <div v-if="technicianTab === 'profile'" class="content">
                        <div class="page-heading">
                            <div>
                                <p class="eyebrow">Technician account</p>
                                <h1>Profile &amp; contact details</h1>
                                <p>Keep your workshop identity and contact information up to date.</p>
                            </div>
                        </div>

                        <section class="panel profile-panel standalone-card">
                            <div class="panel-head">
                                <div><span class="kicker">Personal information</span><h2>Your profile</h2></div>
                                <button class="text-button" @click="toggleEditProfile">
                                    <svg class="icon" width="15" height="15"><use :href="editingProfile ? '#icon-check' : '#icon-edit'"></use></svg>
                                    @{{ editingProfile ? 'Save changes' : 'Edit profile' }}
                                </button>
                            </div>
                            <div class="profile-identity">
                                <div class="large-avatar">@{{ currentUser.avatar_initials || 'AK' }}</div>
                                <div><strong>@{{ currentUser.name }}</strong><p>Senior technician &middot; Active account</p></div>
                            </div>
                            <div class="profile-fields">
                                <template v-if="editingProfile">
                                    <label class="field"><span>Full name</span><input v-model="profileForm.name"></label>
                                    <label class="field"><span>Email address</span><input v-model="profileForm.email" type="email"></label>
                                    <label class="field"><span>Telephone number</span><input v-model="profileForm.phone" type="tel"></label>
                                </template>
                                <template v-else>
                                    <div><span>Full name</span><strong>@{{ currentUser.name }}</strong></div>
                                    <div><span>Email address</span><strong>@{{ currentUser.email }}</strong></div>
                                    <div><span>Telephone number</span><strong>@{{ currentUser.phone || '+1 (202) 555-0166' }}</strong></div>
                                    <div><span>Role</span><strong>Senior technician</strong></div>
                                </template>
                            </div>
                            <div class="workshop-card">
                                <svg class="icon" width="20" height="20"><use href="#icon-tool"></use></svg>
                                <div>
                                    <strong>Technician access</strong>
                                    <p>Assigned repair queue, diagnosis bench, customer update notes, and feedback permissions.</p>
                                </div>
                            </div>
                        </section>
                    </div>
                </template>
            </main>

            <footer>
                &copy; {{ date('Y') }} Fixsmith Repair Studio
                <span>Privacy &middot; Terms &middot; Service guarantee &middot; Automated SMTP Notifications</span>
            </footer>
        </div>

        <!-- SMTP CONFIGURATION & TEST MODAL -->
        <div v-if="smtpModalOpen" class="modal-backdrop" @mousedown="smtpModalOpen = false">
            <section class="account-modal" style="width: min(100%, 680px); max-height: 90vh; overflow-y: auto;" @mousedown.stop>
                <div class="modal-head">
                    <div>
                        <span class="kicker">Email System</span>
                        <h2>SMTP Configuration &amp; Testing</h2>
                        <p>Configure your real SMTP credentials (Gmail, Mailtrap, Brevo, Sendgrid) and send test notifications.</p>
                    </div>
                    <button class="modal-close" @click="smtpModalOpen = false">&times;</button>
                </div>

                <div class="smtp-tab-nav">
                    <button class="smtp-tab-btn" :class="{ active: smtpActiveTab === 'settings' }" @click="smtpActiveTab = 'settings'">
                        SMTP Settings
                    </button>
                    <button class="smtp-tab-btn" :class="{ active: smtpActiveTab === 'test' }" @click="smtpActiveTab = 'test'">
                        Send Test Email
                    </button>
                    <button class="smtp-tab-btn" :class="{ active: smtpActiveTab === 'logs' }" @click="smtpActiveTab = 'logs'; fetchEmailLogs()">
                        Email Activity Logs
                    </button>
                </div>

                <div style="padding: 0 24px 24px;">
                    <!-- TAB 1: SETTINGS -->
                    <form v-if="smtpActiveTab === 'settings'" @submit.prevent="saveSmtpSettings">
                        <div class="account-form-grid" style="margin-top: 0;">
                            <label class="field">
                                <span>Mail Driver / Transport</span>
                                <select v-model="smtpForm.mailer">
                                    <option value="smtp">SMTP (Live Mail Server)</option>
                                    <option value="log">Log Only (Write to storage/logs/laravel.log)</option>
                                </select>
                            </label>

                            <label class="field">
                                <span>SMTP Host</span>
                                <input v-model="smtpForm.host" placeholder="e.g. smtp.mailtrap.io or smtp.gmail.com">
                            </label>

                            <label class="field">
                                <span>SMTP Port</span>
                                <input type="number" v-model.number="smtpForm.port" placeholder="587, 465, or 2525">
                            </label>

                            <label class="field">
                                <span>Encryption</span>
                                <select v-model="smtpForm.encryption">
                                    <option value="tls">TLS (Standard for port 587)</option>
                                    <option value="ssl">SSL (Standard for port 465)</option>
                                    <option value="">None</option>
                                </select>
                            </label>

                            <label class="field">
                                <span>SMTP Username</span>
                                <input v-model="smtpForm.username" placeholder="API key or user email">
                            </label>

                            <label class="field">
                                <span>SMTP Password</span>
                                <input type="password" v-model="smtpForm.password" :placeholder="smtpForm.has_password ? '•••••••• (leave blank to keep current)' : 'Enter SMTP password'">
                            </label>

                            <label class="field">
                                <span>Sender Email (From Address)</span>
                                <input type="email" v-model="smtpForm.from_address" placeholder="support@fixsmith.com" required>
                            </label>

                            <label class="field">
                                <span>Sender Name (From Name)</span>
                                <input v-model="smtpForm.from_name" placeholder="Fixsmith Repair Studio" required>
                            </label>
                        </div>

                        <div class="drag-guidance" style="margin-top: 16px;">
                            <svg class="icon" width="18" height="18"><use href="#icon-info"></use></svg>
                            <p><strong>SMTP Tip:</strong> For local development or testing without an external inbox, select <code>Log Only</code>. For real emails, enter your Mailtrap, Gmail App Password, or SMTP server.</p>
                        </div>

                        <div class="modal-actions" style="margin-top: 18px;">
                            <button type="button" class="button button-secondary" @click="smtpActiveTab = 'test'">Go to Test Email</button>
                            <button type="submit" class="button button-primary" :disabled="savingSmtp">
                                <svg class="icon" width="16" height="16"><use href="#icon-check"></use></svg>
                                @{{ savingSmtp ? 'Saving...' : 'Save SMTP Settings' }}
                            </button>
                        </div>
                    </form>

                    <!-- TAB 2: TEST EMAIL -->
                    <form v-if="smtpActiveTab === 'test'" @submit.prevent="sendTestSmtpEmail">
                        <p style="font-size: 12px; color: var(--muted); margin-bottom: 16px;">
                            Dispatch a real test notification email immediately using your current SMTP credentials to verify server connectivity.
                        </p>

                        <div style="display: grid; gap: 14px;">
                            <label class="field">
                                <span>Recipient Email Address</span>
                                <input type="email" v-model="testEmailRecipient" placeholder="your-email@example.com" required>
                            </label>

                            <label class="field">
                                <span>Custom Test Message (Optional)</span>
                                <textarea v-model="testEmailMessage" placeholder="Leave empty for default branded test notification message..."></textarea>
                            </label>
                        </div>

                        <div v-if="testEmailResult" :style="{ marginTop: '16px', padding: '14px', borderRadius: '10px', background: testEmailResult.success ? '#eff8f2' : '#fff4f2', border: '1px solid ' + (testEmailResult.success ? '#68a17f' : '#dda7a2') }">
                            <strong :style="{ color: testEmailResult.success ? 'var(--forest)' : 'var(--red)' }">
                                @{{ testEmailResult.success ? '✓ Delivery Successful' : '✗ Delivery Failed' }}
                            </strong>
                            <p style="margin: 5px 0 0; font-size: 11px;">@{{ testEmailResult.message }}</p>
                        </div>

                        <div class="modal-actions" style="margin-top: 20px;">
                            <button type="button" class="button button-secondary" @click="smtpModalOpen = false">Close</button>
                            <button type="submit" class="button button-primary" :disabled="sendingTestEmail || !testEmailRecipient">
                                <svg class="icon" width="16" height="16"><use href="#icon-mail"></use></svg>
                                @{{ sendingTestEmail ? 'Dispatching Test Email...' : 'Send Test Email Now' }}
                            </button>
                        </div>
                    </form>

                    <!-- TAB 3: LOGS -->
                    <div v-if="smtpActiveTab === 'logs'">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <span style="font-size: 12px; color: var(--muted);">Latest email delivery audit trail:</span>
                            <button class="button button-secondary" style="min-height: 28px; padding: 0 10px; font-size: 10px;" @click="fetchEmailLogs">Refresh Logs</button>
                        </div>

                        <div style="max-height: 340px; overflow-y: auto; border: 1px solid var(--line); border-radius: 9px;">
                            <table class="log-table">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>To</th>
                                        <th>Subject</th>
                                        <th>Event</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="log in emailLogs" :key="log.id">
                                        <td>
                                            <span class="log-badge" :class="log.status">@{{ log.status }}</span>
                                        </td>
                                        <td>@{{ log.to_email }}</td>
                                        <td style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">@{{ log.subject }}</td>
                                        <td><small>@{{ log.event_type }}</small></td>
                                        <td><small>@{{ new Date(log.created_at).toLocaleTimeString() }}</small></td>
                                    </tr>
                                    <tr v-if="emailLogs.length === 0">
                                        <td colspan="5" style="text-align: center; color: var(--muted); padding: 20px;">No email activity logged yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </template>

    <!-- IMAGE LIGHTBOX MODAL -->
    <div v-if="lightboxOpen" class="image-lightbox" @click="closeLightbox">
        <div class="image-lightbox-wrapper" @click.stop>
            <button class="image-lightbox-close" @click="closeLightbox" aria-label="Close image preview">&times;</button>
            <img :src="lightboxSrc" :alt="lightboxCaption">
            <p v-if="lightboxCaption" class="image-lightbox-caption">@{{ lightboxCaption }}</p>
        </div>
    </div>

    <!-- TOAST NOTIFICATIONS -->
    <div class="toast-container">
        <div v-for="toast in toasts" :key="toast.id" class="toast" :class="toast.type">
            <svg class="icon" width="18" height="18"><use :href="toast.type === 'success' ? '#icon-check' : '#icon-info'"></use></svg>
            <span>@{{ toast.message }}</span>
        </div>
    </div>
</div>

<!-- VUE 3 CDN FOR ZERO-BUILD REACTIVITY -->
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

<script>
const initialData = @json($initialData);

const { createApp, ref, computed, onMounted, onUnmounted } = Vue;

createApp({
    setup() {
        // Auth state
        const authenticated = ref(true); // default previewing authenticated
        const authMode = ref('login');
        const authError = ref('');
        const authForm = ref({ name: '', email: '', phone: '', password: '', confirmPassword: '', remember: true });

        // User & Role state
        const currentUser = ref(initialData.user || { name: 'Maya Chen', email: 'maya.chen@example.com', role: 'customer', avatar_initials: 'MC' });
        const currentRole = ref(initialData.role || 'customer');

        // Navigation state
        const sidebarOpen = ref(false);
        const notificationOpen = ref(false);
        const profileMenuOpen = ref(false);
        const customerTab = ref('overview');
        const adminTab = ref('dashboard');
        const technicianTab = ref('workspace');

        // Data stores
        const repairs = ref(initialData.repairs || []);
        const feedbackList = ref(initialData.feedback || []);
        const accounts = ref(initialData.accounts || []);
        const notifications = ref(initialData.notifications || []);
        const emailLogs = ref([]);

        // Toasts
        const toasts = ref([]);
        const showToast = (message, type = 'success') => {
            const id = Date.now();
            toasts.value.push({ id, message, type });
            setTimeout(() => {
                toasts.value = toasts.value.filter(t => t.id !== id);
            }, 3500);
        };

        // All statuses (Waiting for part precedes Fixing)
        const allStatuses = ['Package received', 'Diagnosing', 'Waiting for part', 'Fixing', 'Pending', 'Finished'];

        // Customer computed & carousel
        const customerRepairs = computed(() => {
            return repairs.value.filter(r => r.customer_email === currentUser.value.email || r.customer_name === currentUser.value.name || currentUser.value.role !== 'customer');
        });

        const customerCompletedCount = computed(() => {
            return customerRepairs.value.filter(r => r.status === 'Finished').length;
        });

        const carouselIndex = ref(0);
        const carouselPaused = ref(false);
        const activeCarouselRepair = computed(() => {
            if (customerRepairs.value.length === 0) return null;
            return customerRepairs.value[carouselIndex.value % customerRepairs.value.length];
        });

        const nextCarousel = () => {
            if (customerRepairs.value.length > 0) {
                carouselIndex.value = (carouselIndex.value + 1) % customerRepairs.value.length;
            }
        };
        const prevCarousel = () => {
            if (customerRepairs.value.length > 0) {
                carouselIndex.value = (carouselIndex.value - 1 + customerRepairs.value.length) % customerRepairs.value.length;
            }
        };

        let carouselTimer = null;
        onMounted(() => {
            carouselTimer = setInterval(() => {
                if (!carouselPaused.value && customerRepairs.value.length > 1) {
                    nextCarousel();
                }
            }, 7000);
        });
        onUnmounted(() => {
            if (carouselTimer) clearInterval(carouselTimer);
        });

        const getTimelineStages = (repair) => {
            if (!repair) return [];
            if (repair.method === 'Mail-in') {
                return allStatuses.filter(s => s !== 'Pending');
            }
            return allStatuses.filter(s => !['Package received', 'Pending'].includes(s));
        };

        const getRepairActiveStep = (repair) => {
            if (!repair) return 0;
            const stages = getTimelineStages(repair);
            const idx = stages.indexOf(repair.status);
            if (idx !== -1) return idx;
            if (repair.status === 'Pending') return 0;
            return repair.active_step ?? 0;
        };

        const expandedCustomerRepairId = ref(customerRepairs.value[0]?.id || 'FX-1048');
        const toggleCustomerRepair = (id) => {
            expandedCustomerRepairId.value = expandedCustomerRepairId.value === id ? null : id;
        };

        // Customer New Repair Form
        const newRepairForm = ref({
            device: '',
            serial: '',
            symptom: '',
            delivery: 'mail',
            photoFiles: [], // Array of { file: File, preview: string, name: string }
            slipFile: null,
            slipPreview: null,
            slipName: '',
        });
        const submittingRepair = ref(false);
        const repairSubmitted = ref(false);
        const lastSubmittedRepairId = ref('');

        const handlePhotoUpload = (e) => {
            const files = Array.from(e.target.files || []);
            if (!files.length) return;

            const remainingSlots = 5 - newRepairForm.value.photoFiles.length;
            if (remainingSlots <= 0) {
                showToast('Maximum 5 symptom photos reached.', 'warning');
                e.target.value = '';
                return;
            }

            const filesToAdd = files.slice(0, remainingSlots);
            if (files.length > remainingSlots) {
                showToast(`Added ${remainingSlots} photos. Limit is 5 photos maximum.`, 'warning');
            }

            filesToAdd.forEach(file => {
                const preview = URL.createObjectURL(file);
                newRepairForm.value.photoFiles.push({ file, preview, name: file.name });
            });
            e.target.value = '';
        };

        const removeSymptomPhoto = (index) => {
            newRepairForm.value.photoFiles.splice(index, 1);
        };

        const handleSlipUpload = (e) => {
            const file = e.target.files[0];
            if (file) {
                newRepairForm.value.slipName = file.name;
                newRepairForm.value.slipFile = file;
                newRepairForm.value.slipPreview = URL.createObjectURL(file);
            }
        };

        const removeSlipFile = () => {
            newRepairForm.value.slipName = '';
            newRepairForm.value.slipFile = null;
            newRepairForm.value.slipPreview = null;
        };

        const submitNewRepair = async () => {
            if (newRepairForm.value.delivery === 'mail' && !newRepairForm.value.slipFile) {
                showToast('Shipping slip is required when choosing Mail to workshop.', 'error');
                return;
            }

            submittingRepair.value = true;
            try {
                const formData = new FormData();
                formData.append('device', newRepairForm.value.device);
                formData.append('serial', newRepairForm.value.serial);
                formData.append('symptom', newRepairForm.value.symptom);
                formData.append('delivery', newRepairForm.value.delivery);

                newRepairForm.value.photoFiles.forEach((item) => {
                    formData.append('device_photos[]', item.file);
                });

                if (newRepairForm.value.slipFile) {
                    formData.append('shipping_slip', newRepairForm.value.slipFile);
                }

                const res = await fetch('/api/repairs', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData,
                });
                const data = await res.json();
                if (data.success) {
                    repairs.value.unshift(data.repair);
                    lastSubmittedRepairId.value = data.repair.id;
                    repairSubmitted.value = true;
                    showToast(`Repair ${data.repair.id} created! Confirmation email dispatched via SMTP.`, 'success');
                    fetchNotifications();
                } else {
                    showToast(data.message || 'Error creating repair', 'error');
                }
            } catch (err) {
                showToast('Failed to connect to server.', 'error');
            } finally {
                submittingRepair.value = false;
            }
        };

        // Feedback / Complaints
        const feedbackForm = ref({
            mode: 'rating',
            repair_id: 'FX-1048',
            rating: 5,
            subject: '',
            message: '',
            attachmentName: '',
            attachmentFile: null,
        });
        const submittingFeedback = ref(false);
        const feedbackSubmitted = ref(false);
        const lastFeedbackId = ref('');

        const handleComplaintAttachment = (e) => {
            const file = e.target.files[0];
            if (file) {
                feedbackForm.value.attachmentName = file.name;
                feedbackForm.value.attachmentFile = file;
            }
        };

        const submitFeedback = async () => {
            submittingFeedback.value = true;
            try {
                const formData = new FormData();
                formData.append('mode', feedbackForm.value.mode);
                formData.append('repair_id', feedbackForm.value.repair_id);
                formData.append('rating', feedbackForm.value.rating);
                formData.append('subject', feedbackForm.value.subject);
                formData.append('message', feedbackForm.value.message);
                if (feedbackForm.value.attachmentFile) formData.append('attachment', feedbackForm.value.attachmentFile);

                const res = await fetch('/api/feedback', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData,
                });
                const data = await res.json();
                if (data.success) {
                    feedbackList.value.unshift(data.feedback);
                    lastFeedbackId.value = data.feedback.id;
                    feedbackSubmitted.value = true;
                    showToast(`${data.feedback.type} ${data.feedback.id} submitted! Alert sent to admin via SMTP.`, 'success');
                    fetchNotifications();
                } else {
                    showToast(data.message || 'Error submitting feedback', 'error');
                }
            } catch (err) {
                showToast('Failed to submit feedback.', 'error');
            } finally {
                submittingFeedback.value = false;
            }
        };

        // Admin Feedback management
        const selectedFeedback = ref(feedbackList.value[0] || null);
        const complaintReplyText = ref('');
        const sendingComplaintReply = ref(false);

        const sendComplaintReply = async () => {
            if (!selectedFeedback.value || !complaintReplyText.value) return;
            sendingComplaintReply.value = true;
            try {
                const res = await fetch(`/api/feedback/${selectedFeedback.value.id}/reply`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ reply: complaintReplyText.value }),
                });
                const data = await res.json();
                if (data.success) {
                    selectedFeedback.value.admin_reply = complaintReplyText.value;
                    selectedFeedback.value.status = 'Resolved';
                    complaintReplyText.value = '';
                    showToast(`Response dispatched to customer via SMTP!`, 'success');
                } else {
                    showToast(data.message || 'Error sending reply', 'error');
                }
            } catch (err) {
                showToast('Failed to send reply.', 'error');
            } finally {
                sendingComplaintReply.value = false;
            }
        };

        const markComplaintStatus = async (status) => {
            if (!selectedFeedback.value) return;
            selectedFeedback.value.status = status;
            showToast(`Marked ${selectedFeedback.value.id} as ${status}.`, 'success');
        };

        // Technician Workspace & Queue
        const technicianAssignedRepairs = computed(() => {
            const name = (currentUser.value && currentUser.value.role === 'technician')
                ? currentUser.value.name
                : (currentUser.value?.name || 'Alex Kim');
            return repairs.value.filter(r => r.technician_name === name);
        });
        const technicianUnfinishedCount = computed(() => {
            return technicianAssignedRepairs.value.filter(r => r.status !== 'Finished').length;
        });
        const technicianWaitingPartsCount = computed(() => {
            return technicianAssignedRepairs.value.filter(r => r.status === 'Waiting for part').length;
        });
        const technicianCompletedCount = computed(() => {
            return technicianAssignedRepairs.value.filter(r => r.status === 'Finished').length;
        });

        const techCarouselIndex = ref(0);
        const techCarouselPaused = ref(false);
        const activeTechCarouselRepair = computed(() => {
            if (technicianAssignedRepairs.value.length === 0) return null;
            return technicianAssignedRepairs.value[techCarouselIndex.value % technicianAssignedRepairs.value.length];
        });
        const nextTechCarousel = () => {
            if (technicianAssignedRepairs.value.length > 0) {
                techCarouselIndex.value = (techCarouselIndex.value + 1) % technicianAssignedRepairs.value.length;
            }
        };
        const prevTechCarousel = () => {
            if (technicianAssignedRepairs.value.length > 0) {
                techCarouselIndex.value = (techCarouselIndex.value - 1 + technicianAssignedRepairs.value.length) % technicianAssignedRepairs.value.length;
            }
        };

        const expandedTechRepairId = ref(technicianAssignedRepairs.value[0]?.id || null);
        const toggleTechRepair = (id) => {
            expandedTechRepairId.value = expandedTechRepairId.value === id ? null : id;
        };
        const openAssignedRepairDetail = (id) => {
            expandedTechRepairId.value = id;
            technicianTab.value = 'assigned';
        };

        const techStatusEdits = ref({});
        const techNoteEdits = ref({});
        const savingTechRepair = ref(null);
        const savedTechRepairId = ref(null);

        const setTechStatus = (repairId, status) => {
            techStatusEdits.value[repairId] = status;
        };

        const saveTechRepairUpdate = async (repair) => {
            savingTechRepair.value = repair.id;
            try {
                const targetStatus = techStatusEdits.value[repair.id] || repair.status;
                const targetNote = techNoteEdits.value[repair.id] || repair.latest_update;

                const res = await fetch(`/api/repairs/${repair.id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ status: targetStatus, note: targetNote }),
                });
                const data = await res.json();
                if (data.success) {
                    repair.status = targetStatus;
                    repair.latest_update = targetNote;
                    savedTechRepairId.value = repair.id;
                    setTimeout(() => savedTechRepairId.value = null, 2500);
                    showToast(`Status updated to ${targetStatus}! Client notified via SMTP.`, 'success');
                } else {
                    showToast(data.message || 'Error updating status', 'error');
                }
            } catch (err) {
                showToast('Failed to update repair status.', 'error');
            } finally {
                savingTechRepair.value = null;
            }
        };

        const technicianFeedbackList = computed(() => {
            const assignedIds = technicianAssignedRepairs.value.map(r => r.id);
            return feedbackList.value.filter(fb => assignedIds.includes(fb.repair_id));
        });

        // Admin Dashboard & Requests
        const activeRepairsCount = computed(() => repairs.value.filter(r => r.status !== 'Finished').length);
        const pendingRepairsCount = computed(() => repairs.value.filter(r => r.status === 'Pending' || r.status === 'Package received').length);
        const waitingPartsCount = computed(() => repairs.value.filter(r => r.status === 'Waiting for part').length);
        const finishedRepairsCount = computed(() => repairs.value.filter(r => r.status === 'Finished').length);

        const requestSearchQuery = ref('');
        const requestPage = ref(1);
        const requestPageSize = ref(4);

        const filteredRepairs = computed(() => {
            const q = requestSearchQuery.value.toLowerCase().trim();
            if (!q) return repairs.value;
            return repairs.value.filter(r =>
                r.id.toLowerCase().includes(q) ||
                r.customer_name.toLowerCase().includes(q) ||
                r.device.toLowerCase().includes(q) ||
                r.serial.toLowerCase().includes(q) ||
                r.issue.toLowerCase().includes(q)
            );
        });

        const requestMaxPage = computed(() => Math.max(1, Math.ceil(filteredRepairs.value.length / requestPageSize.value)));
        const paginatedRepairs = computed(() => {
            const start = (requestPage.value - 1) * requestPageSize.value;
            return filteredRepairs.value.slice(start, start + requestPageSize.value);
        });

        // Lightbox Modal
        const lightboxOpen = ref(false);
        const lightboxSrc = ref('');
        const lightboxCaption = ref('');
        const openLightbox = (src, caption = '') => {
            lightboxSrc.value = src;
            lightboxCaption.value = caption;
            lightboxOpen.value = true;
        };
        const closeLightbox = () => {
            lightboxOpen.value = false;
            lightboxSrc.value = '';
            lightboxCaption.value = '';
        };

        // Admin Request Expansion & Package Receipt
        const expandedAdminRepairId = ref(null);
        const toggleAdminRepairDetail = (id) => {
            expandedAdminRepairId.value = expandedAdminRepairId.value === id ? null : id;
        };

        const receivingPackageId = ref(null);
        const confirmPackageReceived = async (repairId) => {
            receivingPackageId.value = repairId;
            try {
                const res = await fetch(`/api/repairs/${repairId}/receive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const data = await res.json();
                if (data.success) {
                    const r = repairs.value.find(item => item.id === repairId);
                    if (r) {
                        r.status = 'Package received';
                        r.slip_verified = true;
                        r.active_step = 0;
                        r.latest_update = data.repair.latest_update;
                    }
                    showToast(`Package for ${repairId} marked received! Technician assignment is now enabled.`, 'success');
                    fetchNotifications();
                } else {
                    showToast(data.message || 'Error receiving package', 'error');
                }
            } catch (err) {
                showToast('Network error confirming package receipt.', 'error');
            } finally {
                receivingPackageId.value = null;
            }
        };

        const formatImageUrl = (url) => {
            if (!url) return '';
            if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('data:') || url.startsWith('blob:')) {
                return url;
            }
            if (url.startsWith('/storage/')) {
                return url;
            }
            if (url.startsWith('storage/')) {
                return '/' + url;
            }
            if (url.startsWith('/')) {
                return url;
            }
            return '/storage/' + url;
        };

        const isGif = (url) => {
            if (!url) return false;
            return typeof url === 'string' && url.toLowerCase().includes('.gif');
        };

        const getRepairPhotos = (repair) => {
            if (!repair) return [];
            let photos = [];
            if (repair.device_photos) {
                if (Array.isArray(repair.device_photos)) {
                    photos = repair.device_photos;
                } else if (typeof repair.device_photos === 'string') {
                    try {
                        const parsed = JSON.parse(repair.device_photos);
                        if (Array.isArray(parsed)) photos = parsed;
                    } catch (e) {
                        photos = [repair.device_photos];
                    }
                }
            } else if (repair.device_photo) {
                photos = [repair.device_photo];
            }
            return photos.filter(Boolean).map(formatImageUrl);
        };

        const assignTechnicianToRepair = async (repairId, techName) => {
            const currentRepair = repairs.value.find(r => r.id === repairId);
            if (currentRepair && currentRepair.method === 'Mail-in' && currentRepair.status === 'Pending' && techName !== 'Unassigned') {
                showToast('Cannot assign technician: Verify delivery slip and mark Package received first.', 'error');
                return;
            }

            try {
                const res = await fetch(`/api/repairs/${repairId}/assign`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ technician: techName }),
                });
                const data = await res.json();
                if (data.success) {
                    const repair = repairs.value.find(r => r.id === repairId);
                    if (repair) repair.technician_name = techName;
                    showToast(`Assigned ${repairId} to ${techName}. Dispatch email sent!`, 'success');
                } else {
                    showToast(data.message || 'Failed to assign technician.', 'error');
                }
            } catch (err) {
                showToast('Failed to assign technician.', 'error');
            }
        };

        // Accounts
        const customerUsers = computed(() => accounts.value.filter(u => u.role === 'customer'));
        const technicianUsers = computed(() => accounts.value.filter(u => u.role === 'technician'));
        const adminUsers = computed(() => accounts.value.filter(u => u.role === 'admin'));

        // Team Board Workload (Dynamically derived from all technician accounts)
        const teamTechnicians = computed(() => {
            return technicianUsers.value.map(user => ({
                id: String(user.id),
                user_id: user.id,
                name: user.name,
                initials: user.avatar_initials || (user.name ? user.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 'TK'),
                title: user.technician_title || 'Repair technician',
                capacity: user.technician_capacity || 3,
            }));
        });

        const unassignedRepairs = computed(() => {
            return repairs.value.filter(r => !r.technician_name || r.technician_name === 'Unassigned');
        });

        const getJobsForTech = (techName) => {
            return repairs.value.filter(r => r.technician_name === techName);
        };

        // Drag & Drop for Team Workload
        const draggedJobId = ref(null);
        const dropTargetLane = ref(null);

        const handleJobDragStart = (e, job) => {
            if (job.interacted || (job.method === 'Mail-in' && job.status === 'Pending')) {
                e.preventDefault();
                if (job.method === 'Mail-in' && job.status === 'Pending') {
                    showToast('Cannot assign mail-in job until delivery slip is verified and package received.', 'warning');
                }
                return;
            }
            draggedJobId.value = job.id;
            if (e.dataTransfer) {
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', job.id);
            }
        };

        const handleJobDragEnd = () => {
            draggedJobId.value = null;
            dropTargetLane.value = null;
        };

        const handleJobDrop = (targetLane) => {
            const jobId = draggedJobId.value;
            dropTargetLane.value = null;
            draggedJobId.value = null;
            if (!jobId) return;

            const currentJob = repairs.value.find(r => r.id === jobId);
            if (!currentJob) return;

            if (currentJob.method === 'Mail-in' && currentJob.status === 'Pending' && targetLane !== 'unassigned') {
                showToast('Cannot assign mail-in job until delivery slip is verified and package received.', 'warning');
                return;
            }

            let targetTechName = 'Unassigned';
            if (targetLane && targetLane !== 'unassigned') {
                const foundTech = teamTechnicians.value.find(t => String(t.id) === String(targetLane) || t.name === targetLane);
                if (foundTech) targetTechName = foundTech.name;
            }

            if ((targetLane === 'unassigned' && (!currentJob.technician_name || currentJob.technician_name === 'Unassigned')) ||
                (currentJob.technician_name === targetTechName)) {
                return;
            }

            reassignTeamJob(jobId, targetLane);
        };

        const reassignTeamJob = async (jobId, targetTechIdOrSlug) => {
            try {
                const currentJob = repairs.value.find(item => item.id === jobId);
                if (currentJob && currentJob.method === 'Mail-in' && currentJob.status === 'Pending' && targetTechIdOrSlug !== 'unassigned') {
                    showToast('Cannot assign technician: Verify delivery slip and mark Package received first.', 'error');
                    return;
                }

                let techName = 'Unassigned';
                let techId = null;
                if (targetTechIdOrSlug && targetTechIdOrSlug !== 'unassigned') {
                    const found = teamTechnicians.value.find(t => String(t.id) === String(targetTechIdOrSlug) || t.name === targetTechIdOrSlug);
                    if (found) {
                        techName = found.name;
                        techId = found.user_id;
                    } else {
                        const slugMap = { 'alex': 'Alex Kim', 'sam': 'Sam Rivera', 'priya': 'Priya Shah', 'leo': 'Leo Grant' };
                        techName = slugMap[targetTechIdOrSlug] || targetTechIdOrSlug;
                    }
                }

                const res = await fetch('/api/repairs/workload', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ 
                        job_id: jobId, 
                        technician: techName === 'Unassigned' ? null : techName,
                        technician_id: techId 
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    const r = repairs.value.find(item => item.id === jobId);
                    if (r) r.technician_name = techName;
                    showToast(`Reassigned ${jobId} to ${techName}! Notification dispatched via SMTP.`, 'success');
                } else {
                    showToast(data.message || 'Cannot reassign started job.', 'error');
                }
            } catch (err) {
                showToast('Error reassigning job.', 'error');
            }
        };

        const getTechAssignedCount = (techName) => {
            return repairs.value.filter(r => r.technician_name === techName && r.status !== 'Finished').length;
        };

        const showAddAccountForm = ref(false);
        const newAccountForm = ref({ name: '', email: '', password: '', role: 'Customer' });

        const submitNewAccount = async () => {
            try {
                const res = await fetch('/api/accounts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(newAccountForm.value),
                });
                const data = await res.json();
                if (data.success) {
                    accounts.value.push(data.user);
                    newAccountForm.value = { name: '', email: '', password: '', role: 'Customer' };
                    showAddAccountForm.value = false;
                    showToast(`Account created for ${data.user.name}!`, 'success');
                } else {
                    showToast(data.message || 'Error creating account', 'error');
                }
            } catch (err) {
                showToast('Failed to create user account.', 'error');
            }
        };

        const editingUser = ref(null);
        const editAccountForm = ref({ name: '', newPassword: '', active: true });

        const openEditAccountModal = (user) => {
            editingUser.value = user;
            editAccountForm.value = { name: user.name, newPassword: '', active: user.active };
        };

        const saveAccountEdit = async () => {
            if (!editingUser.value) return;
            try {
                const res = await fetch(`/api/accounts/${editingUser.value.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        name: editAccountForm.value.name,
                        password: editAccountForm.value.newPassword,
                        active: editAccountForm.value.active,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    Object.assign(editingUser.value, data.user);
                    editingUser.value = null;
                    showToast('Account changes saved!', 'success');
                } else {
                    showToast(data.message || 'Error saving changes', 'error');
                }
            } catch (err) {
                showToast('Failed to update account.', 'error');
            }
        };

        // Profile Editing
        const editingProfile = ref(false);
        const profileForm = ref({ name: currentUser.value.name, phone: currentUser.value.phone || '', email: currentUser.value.email });

        const toggleEditProfile = async () => {
            if (editingProfile.value) {
                // Save profile
                try {
                    const res = await fetch('/api/profile', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(profileForm.value),
                    });
                    const data = await res.json();
                    if (data.success) {
                        Object.assign(currentUser.value, data.user);
                        showToast('Profile updated successfully!', 'success');
                    }
                } catch (err) {
                    showToast('Error updating profile.', 'error');
                }
            } else {
                // Entering edit mode – always sync form from the live currentUser
                profileForm.value = {
                    name: currentUser.value.name,
                    phone: currentUser.value.phone || '',
                    email: currentUser.value.email,
                };
            }
            editingProfile.value = !editingProfile.value;
        };

        const openProfile = () => {
            // Reset edit state so the profile tab always opens in read mode
            editingProfile.value = false;
            if (currentRole.value === 'customer') customerTab.value = 'profile';
            if (currentRole.value === 'admin') adminTab.value = 'profile';
            if (currentRole.value === 'technician') technicianTab.value = 'profile';
        };

        // Notifications
        const fetchNotifications = async () => {
            try {
                const res = await fetch(`/api/notifications?role=${currentRole.value}`);
                const data = await res.json();
                notifications.value = data;
            } catch (e) {}
        };

        const unreadNotificationCount = computed(() => {
            return notifications.value.filter(n => n.role === currentRole.value && !n.is_read).length;
        });
        const upcomingNotifications = computed(() => {
            return notifications.value.filter(n => n.role === currentRole.value && n.upcoming);
        });
        const recentNotifications = computed(() => {
            return notifications.value.filter(n => n.role === currentRole.value && !n.upcoming);
        });

        const markAllNotificationsRead = async () => {
            try {
                await fetch('/api/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ role: currentRole.value }),
                });
                notifications.value.forEach(n => { if (n.role === currentRole.value) n.is_read = true; });
                showToast('All notifications marked as read.', 'success');
            } catch (e) {}
        };

        const handleNotificationClick = async (item) => {
            item.is_read = true;
            try {
                fetch(`/api/notifications/${item.id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                });
            } catch (e) {}

            notificationOpen.value = false;
            if (currentRole.value === 'customer') {
                if (item.target === 'repairs') {
                    customerTab.value = 'repairs';
                    if (item.entity_id) expandedCustomerRepairId.value = item.entity_id;
                } else {
                    customerTab.value = item.target;
                }
            } else if (currentRole.value === 'admin') {
                adminTab.value = item.target;
            } else if (currentRole.value === 'technician') {
                if (item.target === 'assigned') {
                    technicianTab.value = 'assigned';
                    if (item.entity_id) expandedTechRepairId.value = item.entity_id;
                } else {
                    technicianTab.value = item.target;
                }
            }
        };

        // Navigation helpers
        const navigateCustomer = (tab, repairId = null) => {
            customerTab.value = tab;
            if (repairId) expandedCustomerRepairId.value = repairId;
            sidebarOpen.value = false;
        };
        const navigateAdmin = (tab) => {
            adminTab.value = tab;
            sidebarOpen.value = false;
        };
        const navigateTechnician = (tab) => {
            technicianTab.value = tab;
            sidebarOpen.value = false;
        };

        // Role Switcher
        const switchRole = async (role) => {
            currentRole.value = role;
            try {
                const res = await fetch('/api/auth/preview', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ role }),
                });
                const data = await res.json();
                if (data.success) {
                    currentUser.value = data.user;
                    profileForm.value = { name: data.user.name, phone: data.user.phone || '', email: data.user.email };
                }
            } catch (e) {}

            if (role === 'customer') customerTab.value = 'overview';
            if (role === 'admin') adminTab.value = 'dashboard';
            if (role === 'technician') technicianTab.value = 'workspace';
            fetchNotifications();
            showToast(`Switched active view to ${role.toUpperCase()}`, 'success');
        };

        // Quick Auth / Login
        const quickLogin = (role) => {
            switchRole(role);
            authenticated.value = true;
        };

        const handleAuthSubmit = async () => {
            authError.value = '';
            if (authMode.value === 'register' && authForm.value.password !== authForm.value.confirmPassword) {
                authError.value = 'Passwords do not match.';
                return;
            }

            const endpoint = authMode.value === 'register' ? '/api/auth/register' : '/api/auth/login';
            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(authForm.value),
                });
                const data = await res.json();
                if (data.success) {
                    currentUser.value = data.user;
                    currentRole.value = data.role;
                    authenticated.value = true;
                    showToast(`Signed in as ${data.user.name}!`, 'success');
                } else {
                    authError.value = data.message || 'Authentication error.';
                }
            } catch (err) {
                authError.value = 'Network error during sign-in.';
            }
        };

        const toggleAuthMode = () => {
            authMode.value = authMode.value === 'login' ? 'register' : 'login';
            authError.value = '';
        };

        const logout = async () => {
            try {
                await fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                });
            } catch (e) {}
            authenticated.value = false;
            authMode.value = 'login';
            showToast('You have been logged out.', 'success');
        };

        // SMTP Modal & Testing
        const smtpModalOpen = ref(false);
        const smtpActiveTab = ref('settings');
        const smtpForm = ref({
            mailer: initialData.smtp?.mailer || 'smtp',
            host: initialData.smtp?.host || 'smtp.mailtrap.io',
            port: initialData.smtp?.port || 587,
            encryption: initialData.smtp?.encryption || 'tls',
            username: initialData.smtp?.username || '',
            password: '',
            has_password: Boolean(initialData.smtp?.password),
            from_address: initialData.smtp?.from_address || 'support@fixsmith.com',
            from_name: initialData.smtp?.from_name || 'Fixsmith Repair Studio',
        });
        const savingSmtp = ref(false);

        const saveSmtpSettings = async () => {
            savingSmtp.value = true;
            try {
                const res = await fetch('/api/smtp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(smtpForm.value),
                });
                const data = await res.json();
                if (data.success) {
                    showToast('SMTP settings saved successfully!', 'success');
                } else {
                    showToast(data.message || 'Error saving SMTP', 'error');
                }
            } catch (err) {
                showToast('Failed to save SMTP settings.', 'error');
            } finally {
                savingSmtp.value = false;
            }
        };

        const testEmailRecipient = ref(currentUser.value.email || 'maya.chen@example.com');
        const testEmailMessage = ref('');
        const sendingTestEmail = ref(false);
        const testEmailResult = ref(null);

        const sendTestSmtpEmail = async () => {
            sendingTestEmail.value = true;
            testEmailResult.value = null;
            try {
                const res = await fetch('/api/smtp/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        recipient_email: testEmailRecipient.value,
                        custom_message: testEmailMessage.value,
                    }),
                });
                const data = await res.json();
                testEmailResult.value = data;
                if (data.success) {
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message, 'error');
                }
            } catch (err) {
                testEmailResult.value = { success: false, message: 'Network error communicating with mail server.' };
                showToast('Error during SMTP test.', 'error');
            } finally {
                sendingTestEmail.value = false;
            }
        };

        const fetchEmailLogs = async () => {
            try {
                const res = await fetch('/api/smtp/logs');
                const data = await res.json();
                emailLogs.value = data;
            } catch (e) {}
        };

        // Load logs initially
        fetchEmailLogs();

        return {
            authenticated,
            authMode,
            authError,
            authForm,
            currentUser,
            currentRole,
            sidebarOpen,
            notificationOpen,
            profileMenuOpen,
            customerTab,
            adminTab,
            technicianTab,
            repairs,
            feedbackList,
            accounts,
            notifications,
            emailLogs,
            toasts,
            showToast,
            allStatuses,
            customerRepairs,
            customerCompletedCount,
            carouselIndex,
            carouselPaused,
            activeCarouselRepair,
            nextCarousel,
            prevCarousel,
            getTimelineStages,
            getRepairActiveStep,
            expandedCustomerRepairId,
            toggleCustomerRepair,
            newRepairForm,
            submittingRepair,
            repairSubmitted,
            lastSubmittedRepairId,
            handlePhotoUpload,
            removeSymptomPhoto,
            handleSlipUpload,
            removeSlipFile,
            submitNewRepair,
            feedbackForm,
            submittingFeedback,
            feedbackSubmitted,
            lastFeedbackId,
            handleComplaintAttachment,
            submitFeedback,
            selectedFeedback,
            complaintReplyText,
            sendingComplaintReply,
            sendComplaintReply,
            markComplaintStatus,
            technicianAssignedRepairs,
            technicianUnfinishedCount,
            technicianWaitingPartsCount,
            technicianCompletedCount,
            techCarouselIndex,
            techCarouselPaused,
            activeTechCarouselRepair,
            nextTechCarousel,
            prevTechCarousel,
            expandedTechRepairId,
            toggleTechRepair,
            openAssignedRepairDetail,
            techStatusEdits,
            techNoteEdits,
            savingTechRepair,
            savedTechRepairId,
            setTechStatus,
            saveTechRepairUpdate,
            technicianFeedbackList,
            activeRepairsCount,
            pendingRepairsCount,
            waitingPartsCount,
            finishedRepairsCount,
            requestSearchQuery,
            requestPage,
            requestPageSize,
            filteredRepairs,
            requestMaxPage,
            paginatedRepairs,
            expandedAdminRepairId,
            toggleAdminRepairDetail,
            receivingPackageId,
            confirmPackageReceived,
            getRepairPhotos,
            formatImageUrl,
            isGif,
            lightboxOpen,
            lightboxSrc,
            lightboxCaption,
            openLightbox,
            closeLightbox,
            assignTechnicianToRepair,
            teamTechnicians,
            unassignedRepairs,
            getJobsForTech,
            reassignTeamJob,
            draggedJobId,
            dropTargetLane,
            handleJobDragStart,
            handleJobDragEnd,
            handleJobDrop,
            customerUsers,
            technicianUsers,
            adminUsers,
            getTechAssignedCount,
            showAddAccountForm,
            newAccountForm,
            submitNewAccount,
            editingUser,
            editAccountForm,
            openEditAccountModal,
            saveAccountEdit,
            editingProfile,
            profileForm,
            toggleEditProfile,
            openProfile,
            unreadNotificationCount,
            upcomingNotifications,
            recentNotifications,
            markAllNotificationsRead,
            handleNotificationClick,
            navigateCustomer,
            navigateAdmin,
            navigateTechnician,
            switchRole,
            quickLogin,
            handleAuthSubmit,
            toggleAuthMode,
            logout,
            smtpModalOpen,
            smtpActiveTab,
            smtpForm,
            savingSmtp,
            saveSmtpSettings,
            testEmailRecipient,
            testEmailMessage,
            sendingTestEmail,
            testEmailResult,
            sendTestSmtpEmail,
            fetchEmailLogs,
        };
    }
}).mount('#app');
</script>
</body>
</html>
