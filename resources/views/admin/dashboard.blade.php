@extends('layout.presensi')

@section('content')
    <div class="topBar">
        <div class="topBarRow">
            <div class="profileGroup">
                <div class="avatar" title="Admin">
                    {{ 'A' }}
                </div>
                <div class="profileMeta">
                    <div class="name">Admin PKBM</div>
                    <div class="sub">Partner Sapystem</div>
                </div>
            </div>

            <div class="iconBtn" aria-label="Search">
                <ion-icon name="search-outline" style="font-size:20px;"></ion-icon>
            </div>
        </div>
    </div>

    <div class="sectionTitleRow">
        <h2>Ringkasan Hari ini</h2>
        <div class="badgeDate">{{ \Carbon\Carbon::parse($today)->format('d M Y') }}</div>
    </div>

    <div class="summaryGrid">
        <div class="summaryCard">
            <div class="summaryTop">
                <div class="summaryIcon hadir">
                    <ion-icon name="checkmark-circle" style="font-size:20px;"></ion-icon>
                </div>
                <div class="summaryLabel">HADIR</div>
            </div>
            <div class="summaryCount">{{ $counts['hadir'] ?? 0 }}</div>
        </div>

        <div class="summaryCard">
            <div class="summaryTop">
                <div class="summaryIcon izin">
                    <ion-icon name="time-outline" style="font-size:20px;"></ion-icon>
                </div>
                <div class="summaryLabel">IZIN</div>
            </div>
            <div class="summaryCount">{{ $counts['izin'] ?? 0 }}</div>
        </div>
    </div>

    <div class="cardBox">
        <div class="cardHeadRow">
            <h2>Statistik Mingguan</h2>
            <a class="mutedLink" href="#">Detail</a>
        </div>

        <div class="barChart">
            @foreach(($weekly['days'] ?? []) as $day)
                @php
                    $count = (int) ($day['count'] ?? 0);
                    $height = (int) round($count / max(1, (int) ($weekly['max'] ?? 1))) * 86;
                    // Aktifkan kolom untuk hari ini (berdasarkan index Carbon)
                    $todayLabel = \Carbon\Carbon::now()->format('N'); // 1..7, ISO (Mon..Sun)
                    $dayIndex = array_search($day['label'] ?? '', ['Sen','Sel','Rab','Kam','Jum','Sab','Min'], true);
                    $isToday = $dayIndex !== false && ((int)$dayIndex + 1) === (int)$todayLabel;
                @endphp
                <div class="barCol">
                    <div class="bar {{ $isToday ? 'active' : '' }}" style="height: {{ max(6, $height) }}px;"></div>
                    <div class="barDay">{{ $day['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="activityHeaderRow">
        <h2>Aktivitas Terbaru</h2>
        <a class="mutedLink" href="#">Lihat Semua</a>
    </div>

    <div class="activityList">
        @forelse($latest as $item)
            @php
                $name = $item->siswa->nama_siswa ?? $item->siswa_id ?? 'Siswa';
                $initial = strtoupper(substr((string) $name, 0, 1));
                $pillClass = $item->status_class ?? 'pending';
                $statusLabel = $item->status_label ?? strtoupper((string) $item->status);
                $tgl = \Carbon\Carbon::parse($item->tgl_presensi)->format('d M Y');
                $jam = (string) ($item->jam_mulai ?? '');
            @endphp
            <div class="activityRow">
                <div class="activityLeft">
                    <div class="activityAvatar">{{ $initial }}</div>
                    <div style="min-width:0;">
                        <div class="activityName">{{ $name }}</div>
                        <div class="activityMeta">{{ $tgl }} • {{ $jam }}</div>
                    </div>
                </div>

                <div class="activityRight">
                    <div class="pill {{ $pillClass }}">{{ $statusLabel }}</div>
                    <a class="detailBtn" href="#">DETAIL</a>
                </div>
            </div>
        @empty
            <div class="emptyState">Belum ada data presensi.</div>
        @endforelse
    </div>
@endsection
