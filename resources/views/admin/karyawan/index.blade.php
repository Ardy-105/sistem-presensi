@extends('layout.admin')

<style>
    .statsRow {
        display: flex;
        gap: 10px;
        margin-bottom: 4px;
    }

    .statBox {
        flex: 1;
        background: #fff;
        padding: 14px 12px;
        border-radius: 14px;
        text-align: center;
    }

    .statBox h2 {
        font-size: 26px;
        font-weight: 700;
        margin: 2px 0;
    }

    .statBox.dark {
        background: #1f2a44;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .statBox.dark h2 {
        font-size: 32px;
    }

    .dotIndicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-bottom: 4px;
    }

    .dotIndicator.green { background: #22c55e; }
    .dotIndicator.red   { background: #ef4444; }

    .searchInput {
        width: 100%;
        padding: 11px 14px;
        border-radius: 12px;
        border: none;
        background: #f1f1f1;
        font-size: 14px;
        box-sizing: border-box;
    }

    .btnSortSmall {
        white-space: nowrap;
        padding: 10px 12px;
        background: #fff0f3;
        color: #e0405a;
        border: none;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
    }

    .listContainer { padding-bottom: 80px; }

    .cardItem {
        display: flex;
        align-items: center;
        background: white;
        padding: 13px 12px;
        border-radius: 14px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: background 0.15s;
    }

    .cardItem:active { background: #f7f7f7; }

    .staffAvatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }

    .info { flex: 1; margin-left: 12px; }
    .info .name { font-weight: 600; font-size: 15px; }
    .info .role { font-size: 12px; color: #888; margin-top: 2px; }

    .statusBadge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        letter-spacing: 0.3px;
    }

    .statusBadge.aktif    { background: #d4f8e8; color: #15803d; }
    .statusBadge.nonaktif { background: #fde2e2; color: #dc2626; }

    /* ---- Overlay ---- */
    .drawerOverlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.35);
        z-index: 100;
        animation: fadeIn 0.2s ease;
    }

    .drawerOverlay.show { display: block; }

    /* ---- Bottom Drawer ---- */
    .bottomDrawer {
        position: fixed;
        bottom: 0;
        left: 0; right: 0;
        background: #fff;
        border-radius: 22px 22px 0 0;
        padding: 12px 20px 36px;
        z-index: 101;
        transform: translateY(100%);
        transition: transform 0.3s cubic-bezier(.32,.72,0,1);
        max-width: 480px;
        margin: 0 auto;
    }

    .bottomDrawer.show { transform: translateY(0); }

    .drawerHandle {
        width: 40px; height: 4px;
        background: #e0e0e0;
        border-radius: 99px;
        margin: 0 auto 18px;
    }

    .drawerHeader {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
    }

    .drawerAvatar {
        width: 50px; height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        color: white;
        flex-shrink: 0;
    }

    .drawerName { font-weight: 700; font-size: 16px; }
    .drawerRole { font-size: 13px; color: #888; margin-top: 2px; }

    .drawerDivider {
        height: 1px;
        background: #f0f0f0;
        margin: 14px 0;
    }

    .drawerRow {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .drawerRowTitle { font-weight: 600; font-size: 14px; }
    .drawerRowSub   { font-size: 12px; color: #888; margin-top: 2px; }

    /* Toggle Switch */
    .toggleSwitch { position: relative; display: inline-block; width: 50px; height: 28px; }
    .toggleSwitch input { opacity: 0; width: 0; height: 0; }
    .toggleSlider {
        position: absolute; cursor: pointer;
        inset: 0;
        background: #e0e0e0;
        border-radius: 99px;
        transition: 0.3s;
    }
    .toggleSlider:before {
        content: "";
        position: absolute;
        width: 22px; height: 22px;
        left: 3px; top: 3px;
        background: white;
        border-radius: 50%;
        transition: 0.3s;
        box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    }
    input:checked + .toggleSlider { background: #22c55e; }
    input:checked + .toggleSlider:before { transform: translateX(22px); }

    /* Drawer Buttons */
    .drawerActionBtn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 14px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 10px;
        box-sizing: border-box;
        cursor: pointer;
        transition: opacity 0.15s;
    }

    .drawerActionBtn:active { opacity: 0.7; }

    .drawerActionBtn.primary  { background: #1f2a44; color: white; }
    .drawerActionBtn.danger   { background: #fff0f0; color: #dc2626; }
    .drawerActionBtn.ghost    { background: #f5f5f5; color: #888; }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

@section('content')

<div class="pageHeaderRow">
    <h2>Data Karyawan</h2>
</div>

<!-- Statistik -->
<div class="statsRow">
    <div class="statBox dark">
        <ion-icon name="people-outline" style="font-size:24px;margin-bottom:4px;"></ion-icon>
        <h2>{{ $total }}</h2>
        <div>TOTAL STAFF</div>
    </div>
    <div class="statBox">
        <span class="dotIndicator green"></span>
        <div style="font-size:12px;color:#888;">AKTIF</div>
        <h2>{{ $aktif }}</h2>
        <div style="font-size:12px;color:#888;">Karyawan</div>
    </div>
    <div class="statBox">
        <span class="dotIndicator red"></span>
        <div style="font-size:12px;color:#888;">NONAKTIF</div>
        <h2>{{ $nonaktif }}</h2>
        <div style="font-size:12px;color:#888;">Karyawan</div>
    </div>
</div>

<!-- Search + Sort -->
<form method="GET" style="display:flex;align-items:center;gap:8px;margin:15px 0;">
    <div style="position:relative;flex:1;">
        <ion-icon name="search-outline" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#aaa;font-size:16px;"></ion-icon>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama karyawan..." class="searchInput" style="padding-left:36px;">
    </div>
    <button type="submit" class="btnSortSmall">
        URUTKAN A-Z
    </button>
</form>

<!-- List Header -->
<div style="font-size:12px;font-weight:700;letter-spacing:1px;color:#aaa;margin-bottom:10px;">DAFTAR STAFF</div>

<!-- List -->
<div class="listContainer">
    @foreach($karyawan as $k)
    @php
        $displayName = (string) ($k->nama_lengkap ?? $k->name ?? '');
        $initials = strtoupper(substr($displayName, 0, 2));
        $avatarColors = ['#6366f1','#f59e0b','#10b981','#3b82f6','#ec4899','#8b5cf6','#14b8a6','#f97316'];
        $avatarIndex = count($avatarColors) ? (abs((int) crc32($displayName)) % count($avatarColors)) : 0;
        $avatarBg = $avatarColors[$avatarIndex] ?? '#64748b';
        $isActive = (bool) ($k->is_active ?? true);
    @endphp
    <div class="cardItem" onclick="openDrawer({{ (int) $k->id }}, {{ Illuminate\Support\Js::from($displayName) }}, {{ Illuminate\Support\Js::from((string) ($k->role ?? '')) }}, {{ $isActive ? 'true' : 'false' }})">
        <div class="staffAvatar" style="background:{{ $avatarBg }}; color: white">
            {{ $initials }}
        </div>
        <div class="info">
            <div class="name">{{ $displayName }}</div>
            <div class="role">{{ ucfirst(str_replace('_', ' ', (string) ($k->role ?? ''))) }}</div>
        </div>
        <div class="statusBadge {{ $isActive ? 'aktif' : 'nonaktif' }}">
            {{ $isActive ? 'AKTIF' : 'NONAKTIF' }}
        </div>
        <a href="{{ route('admin.karyawan.edit', $k->id) }}"
           onclick="event.stopPropagation()"
           aria-label="Edit Karyawan"
           style="display:grid;place-items:center;width:28px;height:28px;border-radius:10px;margin-left:6px;color:#94a3b8;text-decoration:none;">
            <ion-icon name="chevron-forward-outline" style="font-size:18px;"></ion-icon>
        </a>
    </div>
    @endforeach
</div>

<!-- Floating Button -->
<a href="{{ route('admin.karyawan.create') }}" class="fabAdd" aria-label="Tambah Karyawan">
    <ion-icon name="add-outline"></ion-icon>
</a>

<!-- Overlay -->
<div class="drawerOverlay" id="drawerOverlay" onclick="closeDrawer()"></div>

<!-- Bottom Drawer -->
<div class="bottomDrawer" id="bottomDrawer">
    <div class="drawerHandle"></div>

    <!-- Info Karyawan -->
    <div class="drawerHeader">
        <div class="drawerAvatar" id="drawerAvatar"></div>
        <div>
            <div class="drawerName" id="drawerName"></div>
            <div class="drawerRole" id="drawerRole"></div>
        </div>
    </div>

    <div class="drawerDivider"></div>

    <!-- Toggle Status -->
    <div class="drawerRow">
        <div>
            <div class="drawerRowTitle">Status Karyawan</div>
            <div class="drawerRowSub" id="drawerStatusLabel">Aktif</div>
        </div>
        <label class="toggleSwitch">
            <input type="checkbox" id="drawerToggle" onchange="updateStatus()">
            <span class="toggleSlider"></span>
        </label>
    </div>

    <div class="drawerDivider"></div>

    <!-- Tombol Edit -->
    <a id="drawerEditBtn" href="#" class="drawerActionBtn primary">
        <ion-icon name="create-outline"></ion-icon>
        Edit Data Karyawan
    </a>

    <!-- Tombol Hapus -->
    <form id="drawerDeleteForm" method="POST" onsubmit="return confirm('Yakin ingin menghapus karyawan ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="drawerActionBtn danger" style="width:100%;border:none;cursor:pointer;">
            <ion-icon name="trash-outline"></ion-icon>
            Hapus Karyawan
        </button>
    </form>

    <button onclick="closeDrawer()" class="drawerActionBtn ghost">
        Batal
    </button>
</div>

<!-- Hidden Form untuk Toggle Status -->
<form id="statusForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="is_active" id="statusValue">
</form>

@endsection

<script>
    let currentId = null;

    // Helper warna avatar konsisten berdasarkan nama
    const colors = ['#6366f1','#f59e0b','#10b981','#3b82f6','#ec4899','#8b5cf6','#14b8a6','#f97316'];
    function strColor(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) hash = str.charCodeAt(i) + ((hash << 5) - hash);
        return colors[Math.abs(hash) % colors.length];
    }

    function openDrawer(id, nama, role, isActive) {
        currentId = id;
        const initials = nama.substring(0, 2).toUpperCase();
        const color = strColor(nama);

        document.getElementById('drawerAvatar').textContent = initials;
        document.getElementById('drawerAvatar').style.background = color;
        document.getElementById('drawerName').textContent = nama;
        document.getElementById('drawerRole').textContent = role.replace('_', ' ');

        const toggle = document.getElementById('drawerToggle');
        toggle.checked = isActive;
        updateStatusLabel(isActive);

        document.getElementById('drawerEditBtn').href = `/admin/karyawan/${id}/edit`;
        document.getElementById('drawerDeleteForm').action = `/admin/karyawan/${id}`;

        document.getElementById('drawerOverlay').classList.add('show');
        document.getElementById('bottomDrawer').classList.add('show');
    }

    function closeDrawer() {
        document.getElementById('drawerOverlay').classList.remove('show');
        document.getElementById('bottomDrawer').classList.remove('show');
        currentId = null;
    }

    function updateStatusLabel(isActive) {
        const label = document.getElementById('drawerStatusLabel');
        label.textContent = isActive ? 'Karyawan saat ini Aktif' : 'Karyawan saat ini Nonaktif';
        label.style.color = isActive ? '#15803d' : '#dc2626';
    }

    function updateStatus() {
        const toggle = document.getElementById('drawerToggle');
        const isActive = toggle.checked;
        updateStatusLabel(isActive);

        if (!currentId) return;

        const form = document.getElementById('statusForm');
        form.action = `/admin/karyawan/${currentId}/status`;
        document.getElementById('statusValue').value = isActive ? '1' : '0';
        form.submit();
    }
</script>
