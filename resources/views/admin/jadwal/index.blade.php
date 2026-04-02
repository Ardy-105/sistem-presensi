@extends('layout.admin')

@section('title', 'Jadwal — Admin')

<style>
    /* ── Week Strip ── */
    .weekStrip {
        background: #1a3a5c;
        padding: 12px 16px 18px;
    }

    .weekStripDate {
        font-size: 12px;
        color: #a8b8cc;
        margin-bottom: 12px;
        font-weight: 500;
    }

    .weekRow {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .dayCell {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        flex: 1;
    }

    .dayLabel {
        font-size: 10px;
        font-weight: 600;
        color: #7a8fa6;
        letter-spacing: 0.5px;
    }

    .dayNumber {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
        color: #c8d6e5;
        position: relative;
    }

    .dayNumber.selected {
        background: #2563eb;
        color: #fff;
    }

    .dayDot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #2563eb;
        position: absolute;
        bottom: -8px;
    }

    .dayNumber.selected .dayDot {
        background: #fff;
    }

    /* ── Section Header ── */
    .jadwalHeader {
        padding: 16px 16px 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .jadwalHeaderTitle {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.5px;
        color: #64748b;
    }

    .jadwalCount {
        font-size: 11px;
        color: #64748b;
    }

    /* ── Schedule Card ── */
    .scheduleList {
        padding: 0 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding-bottom: 110px;
    }

    .scheduleCard {
        background: #fff;
        border-radius: 14px;
        padding: 16px;
        border-left: 4px solid #2563eb;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        position: relative;
    }

    .scheduleCardTop {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .scheduleTime {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    .scheduleTime ion-icon {
        font-size: 13px;
    }

    .scheduleActions {
        display: flex;
        gap: 6px;
    }

    .scheduleActionBtn {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        font-size: 13px;
    }

    .scheduleActionBtn.edit {
        background: rgba(37,99,235,0.1);
        color: #2563eb;
    }

    .scheduleActionBtn.delete {
        background: rgba(239,68,68,0.1);
        color: #ef4444;
    }

    .scheduleSubject {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .scheduleMeta {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .scheduleMetaRow {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
    }

    .scheduleMetaRow ion-icon {
        font-size: 13px;
        color: #94a3b8;
        flex-shrink: 0;
    }

    .scheduleMetaRow .metaText {
        color: #64748b;
    }

    .scheduleMetaRow .metaHighlight {
        color: #2563eb;
        font-weight: 500;
    }

    /* ── Empty State ── */
    .emptySchedule {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 50px 20px;
        gap: 10px;
        color: #94a3b8;
    }

    .emptySchedule ion-icon {
        font-size: 44px;
        color: #cbd5e1;
    }

    .emptyScheduleText {
        font-size: 14px;
        font-weight: 500;
    }

    /* ── Flash ── */
    .flashAlert {
        margin: 12px 16px 0;
        padding: 11px 14px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
        border: 1px solid;
    }

    .flashAlert.success {
        background: rgba(22,163,74,0.10);
        border-color: rgba(22,163,74,0.25);
        color: #15803d;
    }
</style>

@section('content')

    {{-- ── Week Strip (Header) ── --}}
    @php
        $dayLabels = ['SEN','SEL','RAB','KAM','JUM','SAB'];
    @endphp

    <div style="background:#1a3a5c; padding:14px 16px 8px;">
        <div style="font-size:11px;color:#ccd6e8;font-weight:600;letter-spacing:0.5px;margin-bottom:6px;">
            PENJADWALAN
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:15px;font-weight:700;color:#fff;">
                    {{ $selectedDate->translatedFormat('l') }}
                </div>
                <div style="font-size:12px;color:#a8b8cc;margin-top:2px;">
                    {{ $selectedDate->translatedFormat('d F Y') }}
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                <button class="iconBtn iconBtn--notify" onclick="alert('Notifikasi')">
                    <ion-icon name="notifications-outline"></ion-icon>
                </button>
            </div>
        </div>
    </div>

    <div class="weekStrip">
        <div class="weekRow">
            @foreach($weekDays as $i => $day)
                @php
                    $isSelected = $day->toDateString() === $selectedDate->toDateString();
                    $hasJadwal  = isset($weekCounts[$day->toDateString()]) && $weekCounts[$day->toDateString()] > 0;
                @endphp
                <a href="{{ route('admin.jadwal.index', ['tanggal' => $day->toDateString()]) }}"
                   class="dayCell">
                    <span class="dayLabel">{{ $dayLabels[$i] }}</span>
                    <div class="dayNumber {{ $isSelected ? 'selected' : '' }}">
                        {{ $day->day }}
                        @if($hasJadwal && !$isSelected)
                            <span class="dayDot"></span>
                        @elseif($hasJadwal && $isSelected)
                            <span class="dayDot"></span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="flashAlert success">{{ session('success') }}</div>
    @endif

    {{-- Section Header --}}
    <div class="jadwalHeader">
        <div class="jadwalHeaderTitle">JADWAL HARI INI</div>
        <div class="jadwalCount">{{ $jadwals->count() }} sesi</div>
    </div>

    {{-- Schedule List --}}
    <div class="scheduleList">
        @forelse($jadwals as $jadwal)
            @php
                $tutorName  = $jadwal->tutor->nama_lengkap ?? '-';
                $siswaName  = $jadwal->siswa->nama_siswa ?? '-';
                $initial    = strtoupper(substr((string)$tutorName, 0, 1));
                $jamMulai   = \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i');
                $jamSelesai = \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i');
            @endphp
            <div class="scheduleCard">
                <div class="scheduleCardTop">
                    <div class="scheduleTime">
                        <ion-icon name="time-outline"></ion-icon>
                        {{ $jamMulai }} – {{ $jamSelesai }}
                    </div>
                    <div class="scheduleActions">
                        <a href="{{ route('admin.jadwal.edit', $jadwal) }}"
                           class="scheduleActionBtn edit" title="Edit">
                            <ion-icon name="create-outline"></ion-icon>
                        </a>
                        <form method="POST"
                              action="{{ route('admin.jadwal.destroy', $jadwal) }}"
                              onsubmit="return confirm('Yakin hapus jadwal ini?')"
                              style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="scheduleActionBtn delete" title="Hapus">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="scheduleSubject">
                    {{ $tutorName }}
                </div>

                <div class="scheduleMeta">
                    <div class="scheduleMetaRow">
                        <ion-icon name="person-outline"></ion-icon>
                        <span class="metaText">Tutor: </span>
                        <span class="metaText">{{ $tutorName }}</span>
                    </div>
                    <div class="scheduleMetaRow">
                        <ion-icon name="people-outline"></ion-icon>
                        <span class="metaHighlight">{{ $siswaName }}</span>
                    </div>
                    <div class="scheduleMetaRow">
                        <ion-icon name="calendar-outline"></ion-icon>
                        <span class="metaText">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="emptySchedule">
                <ion-icon name="calendar-outline"></ion-icon>
                <div class="emptyScheduleText">Tidak ada jadwal hari ini</div>
                <div style="font-size:12px;">Klik tombol + untuk menambahkan jadwal</div>
            </div>
        @endforelse
    </div>

    {{-- FAB Add --}}
    <a href="{{ route('admin.jadwal.create') }}" class="fabAdd" aria-label="Tambah Jadwal">
        <ion-icon name="add-outline"></ion-icon>
    </a>

@endsection
