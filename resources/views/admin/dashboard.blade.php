@extends('layout.admin')

@section('content')
<div class="page-wrapper">

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="content">

        {{-- Ringkasan Hari Ini --}}
        <div class="sectionTitleRow">
            <h2>Ringkasan Hari ini</h2>
            <div class="badgeDate">{{ \Carbon\Carbon::parse($today)->translatedFormat('d M Y') }}</div>
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
                <ion-icon class="summaryBigIcon" name="checkmark-circle"></ion-icon>
            </div>

            <div class="summaryCard">
                <div class="summaryTop">
                    <div class="summaryIcon izin">
                        <ion-icon name="time-outline" style="font-size:20px;"></ion-icon>
                    </div>
                    <div class="summaryLabel">IZIN</div>
                </div>
                <div class="summaryCount">{{ $counts['izin'] ?? 0 }}</div>
                <ion-icon class="summaryBigIcon" name="document-text-outline"></ion-icon>
            </div>
        </div>

        {{-- Statistik Mingguan --}}
        <div class="cardBox">
            <div class="cardHeadRow">
                <h2>Statistik Mingguan</h2>
                <a class="mutedLink" href="#">Detail &rsaquo;</a>
            </div>

            <div class="barChart">
                @foreach(($weekly['days'] ?? []) as $day)
                    @php
                        $count  = (int) ($day['count'] ?? 0);
                        $maxVal = (int) ($weekly['max'] ?? 1);
                        $height = $maxVal > 0 ? (int) round(($count / $maxVal) * 86) : 0;

                        $todayISO  = (int) \Carbon\Carbon::now()->format('N'); // 1=Mon…7=Sun
                        $dayLabels = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
                        $dayIndex  = array_search($day['label'] ?? '', $dayLabels, true);
                        $isToday   = $dayIndex !== false && ((int)$dayIndex + 1) === $todayISO;
                    @endphp
                    <div class="barCol">
                        <div class="bar {{ $isToday ? 'active' : '' }}"
                             style="height: {{ max(6, $height) }}px;"></div>
                        <div class="barDay">{{ $day['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Aktivitas Terbaru --}}
        <div class="activityHeaderRow">
            <h2>Aktivitas Terbaru</h2>
            <a class="mutedLink" href="#">Lihat Semua &rsaquo;</a>
        </div>

        <div class="activityList">
            @forelse($latest as $item)
                @php
                    $name        = $item->siswa->nama_siswa ?? $item->siswa_id ?? 'Siswa';
                    $initial     = strtoupper(substr((string) $name, 0, 1));
                    $pillClass   = $item->status_class ?? 'pending';
                    $statusLabel = $item->status_label ?? strtoupper((string) $item->status);
                    $tgl         = \Carbon\Carbon::parse($item->tgl_presensi)->translatedFormat('d M Y');
                    $jam         = (string) ($item->jam_mulai ?? '');
                    $role        = $item->siswa->role ?? '';
                @endphp
                <div class="activityRow">
                    <div class="activityLeft">
                        <div class="activityAvatar" data-initial="{{ $initial }}">{{ $initial }}</div>
                        <div style="min-width:0;">
                            <div class="activityName">{{ $name }}</div>
                            <div class="activityMeta">
                                {{ $jam }}{{ $jam && $role ? ' · ' : '' }}{{ $role }}
                            </div>
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

    </div>{{-- end .content --}}

</div>{{-- end .page-wrapper --}}
@endsection
