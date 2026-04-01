<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#0B5ED7">
    <title>@yield('title', 'Smart Presensi — Admin')</title>

    <style>
        :root {
            --blue: #0B5ED7;
            --blue2: #1A73E8;
            --bg: #bdefff;
            --text: #0f172a;
            --muted: #64748b;
            --card: #ffffff;
            --border: #e5e7eb;
            --success: #16a34a;
            --warn: #f59e0b;
            --danger: #ef4444;
            --radius-xl: 18px;
            --radius-lg: 14px;
            --radius-md: 12px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0; padding: 0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        #appCapsule {
            max-width: 430px;
            margin: 0 auto;
            min-height: 100vh;
            background: var(--card);
            padding-bottom: 92px;
        }

        /* ── Top Bar ── */
        .topBar {
            background: #ffffff;
            color: var(--text);
            padding: 14px 16px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.95);
        }

        .topBarRow { display: flex; align-items: center; justify-content: space-between; gap: 12px; }

        .profileGroup { display: flex; align-items: center; gap: 12px; }

        .avatar {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: #ffedd5;
            display: grid; place-items: center;
            font-weight: 800; color: #9a3412;
            border: 3px solid rgba(255,255,255,0.35);
        }

        .profileMeta { display: flex; flex-direction: column; line-height: 1.1; }
        .profileMeta .name { font-size: 13px; font-weight: 700; }
        .profileMeta .sub  { font-size: 11px; color: var(--muted); }

        .topIcons { display: flex; align-items: center; gap: 6px; }

        .iconBtn {
            width: 36px; height: 36px;
            border-radius: 999px;
            background: transparent;
            display: grid; place-items: center;
            border: none;
            color: #0f172a;
            cursor: pointer;
            position: relative;
            padding: 0;
        }

        .iconBtn ion-icon {
            font-size: 20px;
            color: #0f172a;
        }

        .iconBtn::after {
            content: "";
            position: absolute;
            width: 7px;
            height: 7px;
            border-radius: 999px;
            top: 5px;
            right: 6px;
            border: 2px solid #fff;
        }

        .iconBtn--notify::after { background: #ef4444; }
        .iconBtn--search::after { background: #1d4ed8; }

        .fabAdd {
            position: fixed;
            right: max(18px, calc((100vw - 430px) / 2 + 18px));
            bottom: 86px;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            border: 1px solid rgba(11,94,215,0.25);
            background: #001a72;
            color: #ffffff;
            display: grid;
            place-items: center;
            text-decoration: none;
            box-shadow: 0 10px 24px rgba(1, 16, 74, 0.28);
            z-index: 950;
        }

        .fabAdd ion-icon {
            font-size: 26px;
            font-weight: 900;
        }

        /* ── Section Title ── */
        .sectionTitleRow {
            padding: 16px 16px 10px;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }

        h2 { margin: 0; font-size: 14px; letter-spacing: 0.2px; }

        .badgeDate {
            font-size: 12px; padding: 7px 10px;
            border-radius: 14px;
            background: rgba(239,68,68,0.12); color: #dc2626;
            font-weight: 700; border: 1px solid rgba(239,68,68,0.2);
            white-space: nowrap;
        }

        /* ── Summary Grid ── */
        .summaryGrid {
            display: grid; grid-template-columns: repeat(2, minmax(0,1fr));
            gap: 12px; padding: 0 16px 14px;
        }

        .summaryCard {
            border: 1px solid var(--border); border-radius: var(--radius-lg);
            padding: 12px; display: flex; flex-direction: column; gap: 8px;
            background: #f8fbff;
        }

        .summaryTop { display: flex; align-items: center; justify-content: space-between; gap: 10px; }

        .summaryIcon { width: 42px; height: 42px; border-radius: 14px; display: grid; place-items: center; font-size: 18px; }
        .summaryIcon.hadir     { background: rgba(22,163,74,0.12);   color: var(--success); }
        .summaryIcon.sakit     { background: rgba(239,68,68,0.12);   color: var(--danger);  }
        .summaryIcon.izin      { background: rgba(245,158,11,0.14);  color: var(--warn);    }
        .summaryIcon.terlambat { background: rgba(100,116,139,0.14); color: #334155;        }

        .summaryLabel { font-size: 12px; color: var(--muted); font-weight: 800; letter-spacing: 0.2px; }
        .summaryCount { font-size: 26px; font-weight: 900; color: var(--text); }

        /* ── Card Box ── */
        .cardBox {
            border: 1px solid var(--border); border-radius: var(--radius-xl);
            background: #f8fbff; margin: 0 16px 14px; padding: 12px 12px 14px;
        }

        .cardHeadRow { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding-bottom: 10px; }

        .mutedLink { color: var(--muted); font-size: 12px; font-weight: 700; text-decoration: none; }

        /* ── Bar Chart ── */
        .barChart { display: flex; align-items: flex-end; gap: 10px; height: 86px; padding: 6px 4px 0; }
        .barCol { display: flex; flex-direction: column; align-items: center; width: 24px; gap: 6px; }
        .bar { width: 100%; border-radius: 10px 10px 0 0; background: #e5e7eb; }
        .bar.active { background: var(--blue2); }
        .barDay { font-size: 11px; color: var(--muted); font-weight: 800; }

        /* ── Activity ── */
        .activityHeaderRow {
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; padding: 2px 16px 10px;
        }

        .activityList { padding: 0 16px 6px; }

        .activityRow {
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; border: 1px solid rgba(229,231,235,0.9);
            border-radius: 16px; padding: 12px; background: #fbfdff; margin-bottom: 10px;
        }

        .activityLeft { display: flex; align-items: center; gap: 12px; min-width: 0; }

        .activityAvatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: #ffedd5; display: grid; place-items: center;
            font-weight: 900; color: #9a3412; flex-shrink: 0;
        }

        .activityName { font-size: 13px; font-weight: 900; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 200px; }
        .activityMeta { font-size: 11px; color: var(--muted); margin-top: 3px; white-space: nowrap; }

        .activityRight { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; flex-shrink: 0; }

        /* ── Pills ── */
        .pill { font-size: 11px; font-weight: 900; padding: 6px 10px; border-radius: 999px; border: 1px solid transparent; letter-spacing: 0.2px; white-space: nowrap; }
        .pill.hadir     { background: rgba(22,163,74,0.12);   color: #15803d; border-color: rgba(22,163,74,0.2);   }
        .pill.sakit     { background: rgba(239,68,68,0.12);   color: #dc2626; border-color: rgba(239,68,68,0.2);   }
        .pill.izin      { background: rgba(245,158,11,0.15);  color: #d97706; border-color: rgba(245,158,11,0.2);  }
        .pill.terlambat { background: rgba(100,116,139,0.12); color: #475569; border-color: rgba(100,116,139,0.2); }
        .pill.pending   { background: rgba(100,116,139,0.10); color: #64748b; border-color: rgba(100,116,139,0.18);}

        .detailBtn {
            font-size: 11px; font-weight: 900; padding: 7px 10px;
            border-radius: 12px; background: rgba(11,94,215,0.10);
            color: var(--blue2); border: 1px solid rgba(11,94,215,0.18); text-decoration: none;
        }

        .emptyState {
            margin: 10px 0 20px; padding: 14px; text-align: center;
            border-radius: 16px; border: 1px dashed rgba(100,116,139,0.35);
            color: var(--muted); font-weight: 800; font-size: 12px;
        }

        /* ── Flash Alert ── */
        .flashAlert { margin: 12px 16px 0; padding: 10px 12px; border-radius: 14px; font-size: 12px; font-weight: 800; }
        .flashAlert.success { border: 1px solid rgba(22,163,74,0.25);  background: rgba(22,163,74,0.10);  color: #166534; }
        .flashAlert.warning { border: 1px solid rgba(245,158,11,0.25); background: rgba(245,158,11,0.12); color: #92400e; }

        /* ── Form / CRUD Utilities ── */
        .pageHeaderRow { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 16px 10px; }

        .btnPrimary {
            background: var(--blue2); color: #fff; border: none;
            border-radius: 14px; padding: 10px 12px;
            font-weight: 900; font-size: 12px;
            text-decoration: none; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;
        }

        .btnOutlineDanger {
            background: rgba(239,68,68,0.10); color: #dc2626;
            border: 1px solid rgba(239,68,68,0.22);
            border-radius: 14px; padding: 9px 10px;
            font-weight: 900; font-size: 12px; text-decoration: none;
        }

        .btnOutline {
            background: #fff; color: var(--blue2);
            border: 1px solid rgba(11,94,215,0.18);
            border-radius: 14px; padding: 9px 10px;
            font-weight: 900; font-size: 12px; text-decoration: none;
        }

        .formCard { margin: 0 16px 16px; border: 1px solid var(--border); border-radius: var(--radius-xl); background: #f8fbff; padding: 14px; }

        .fieldLabel { font-size: 12px; font-weight: 900; margin-bottom: 6px; color: #334155; }

        .input {
            width: 100%; padding: 11px 12px;
            border-radius: 14px; border: 1px solid rgba(229,231,235,1);
            background: #fff; outline: none; font-size: 13px;
        }

        .input:focus { border-color: rgba(11,94,215,0.45); box-shadow: 0 0 0 3px rgba(11,94,215,0.15); }

        .formRow { display: flex; flex-direction: column; gap: 10px; margin-bottom: 12px; }

        .errorList {
            margin: 0 16px 12px; padding: 10px 12px;
            border-radius: 16px; border: 1px solid rgba(239,68,68,0.25);
            background: rgba(239,68,68,0.08); color: #dc2626; font-weight: 900; font-size: 12px;
        }

        .siswaGrid { padding: 0 16px 10px; display: grid; grid-template-columns: 1fr; gap: 10px; }
        .siswaCard { border: 1px solid rgba(229,231,235,0.9); border-radius: 18px; padding: 12px; background: #ffffff; }
        .siswaTop { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .siswaName { font-weight: 1000; font-size: 14px; margin-top: 2px; }
        .siswaMeta { margin-top: 3px; color: var(--muted); font-size: 11px; font-weight: 900; }
        .siswaActions { display: flex; gap: 10px; margin-top: 12px; flex-wrap: wrap; }
        .smallBtn { padding: 9px 10px; border-radius: 14px; font-weight: 1000; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .smallBtn.edit   { border: 1px solid rgba(11,94,215,0.18); color: var(--blue2); background: rgba(11,94,215,0.06); }
        .smallBtn.delete { border: 1px solid rgba(239,68,68,0.22); color: #dc2626;      background: rgba(239,68,68,0.08); }

        /* ============================================================
           BOTTOM NAV — ADMIN (sesuai desain foto)
           Dashboard | ID Card | Kelas | Kalender | Statistik
        ============================================================ */
        .bottomNavAdmin {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            margin: 0 auto;
            max-width: 430px;
            background: #ffffff;
            border-top: 1px solid rgba(229,231,235,0.9);
            padding: 10px 10px 14px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 999;
        }

        .bottomNavAdmin a {
            text-decoration: none;
            color: #94a3b8;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            font-weight: 700;
            font-size: 10px;
            flex: 1;
            padding: 4px 0;
            border-radius: 12px;
            transition: color 0.15s;
        }

        .bottomNavAdmin a ion-icon { font-size: 24px; }
        .bottomNavAdmin a.active   { color: var(--blue2); }
    </style>
</head>
<body>
    <div id="appCapsule">
        <div class="topBar">
            <div class="topBarRow">
                <div class="profileGroup">
                    <div class="avatar" title="Admin">A</div>
                    <div class="profileMeta">
                        <div class="name">Admin PKBM</div>
                        <div class="sub">Pintar Presence System</div>
                    </div>
                </div>
                <div class="topIcons">
                    <button class="iconBtn iconBtn--notify" aria-label="Notifikasi">
                        <ion-icon name="notifications-outline"></ion-icon>
                    </button>
                    <button class="iconBtn iconBtn--search" aria-label="Cari">
                        <ion-icon name="search-outline"></ion-icon>
                    </button>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="flashAlert success">{{ session('success') }}</div>
        @endif
        @if (session('warning'))
            <div class="flashAlert warning">{{ session('warning') }}</div>
        @endif

        @yield('content')
    </div>

    {{--  Top Header  --}}


    {{-- ── Bottom Nav Admin (sesuai foto) ── --}}
    <nav class="bottomNavAdmin">
        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           aria-label="Dashboard">
            <ion-icon name="{{ request()->routeIs('admin.dashboard') ? 'grid' : 'grid-outline' }}"></ion-icon>
        </a>

        <a href="{{ route('admin.karyawan.index') }}"
           class="{{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}"
           aria-label="Data Karyawan">
            <ion-icon name="{{ request()->routeIs('admin.karyawan.*') ? 'id-card' : 'id-card-outline' }}"></ion-icon>
        </a>

        <a href="{{ route('admin.siswa.index') }}"
           class="{{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}"
           aria-label="Data Siswa">
            <ion-icon name="{{ request()->routeIs('admin.siswa.*') ? 'school' : 'school-outline' }}"></ion-icon>
        </a>

        <a href="#"
           class="{{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}"
           aria-label="Jadwal">
            <ion-icon name="{{ request()->routeIs('admin.jadwal.*') ? 'calendar' : 'calendar-outline' }}"></ion-icon>
        </a>

        <a href="#"
           class="{{ request()->routeIs('admin.statistik.*') ? 'active' : '' }}"
           aria-label="Statistik">
            <ion-icon name="{{ request()->routeIs('admin.statistik.*') ? 'bar-chart' : 'bar-chart-outline' }}"></ion-icon>
        </a>
    </nav>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
