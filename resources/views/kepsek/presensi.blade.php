@extends('layout.kepsek')

@section('title', 'Data Presensi — Kepala Sekolah')

@section('content')
<div class="sectionTitleRow">
    <h2>Data Presensi</h2>
</div>

<div class="activityList">
    @forelse($presensi as $item)
        @php
            $tutorName   = $item->tutor->nama_lengkap ?? 'Tutor';
            $siswaName   = $item->siswa->nama_siswa ?? $item->siswa_id ?? 'Siswa';
            $initial     = strtoupper(substr((string) $tutorName, 0, 1));
            $pillClass   = 'pending';
            $statusLabel = 'BELUM ABSEN';

            if ($item->status === 'alpha') {
                $statusLabel = 'ALPHA'; $pillClass = 'alpha';
            } elseif ($item->foto_mulai && $item->foto_selesai) {
                $statusLabel = 'SELESAI'; $pillClass = 'hadir';
            } elseif ($item->foto_mulai) {
                $statusLabel = 'SEDANG BERJALAN'; $pillClass = 'izin';
            }

            $tgl = \Carbon\Carbon::parse($item->tgl_presensi)->translatedFormat('d M Y');
            $jam = (string) ($item->jam_mulai ?? '');
        @endphp
        <div class="activityRow">
            <div class="activityLeft">
                <div class="activityAvatar">
                    @if($item->tutor->foto)
                        <img src="{{ asset($item->tutor->foto) }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;" />
                    @else
                        {{ $initial }}
                    @endif
                </div>
                <div style="min-width:0;">
                    <div class="activityName">{{ $tutorName }}</div>
                    <div class="activityMeta">{{ $siswaName }}{{ $jam ? ' · ' . $jam : '' }} · {{ $tgl }}</div>
                </div>
            </div>
            <div class="activityRight">
                <div class="pill {{ $pillClass }}">{{ $statusLabel }}</div>
            </div>
        </div>
    @empty
        <div class="emptyState">Belum ada data presensi.</div>
    @endforelse
</div>

{{-- Pagination --}}
@if($presensi->hasPages())
<div style="padding: 0 16px 16px; display: flex; gap: 8px; flex-wrap: wrap;">
    {{ $presensi->links() }}
</div>
@endif
@endsection
