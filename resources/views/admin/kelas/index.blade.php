@extends('layout.admin')

@section('content')
    <div class="pageHeaderRow">
        <h2>Data Kelas</h2>
    </div>

    @if (session('success'))
        <div class="errorList" style="border-color: rgba(22,163,74,0.25); background: rgba(22,163,74,0.10); color:#15803d;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="errorList" style="border-color: rgba(245,158,11,0.30); background: rgba(245,158,11,0.12); color:#92400e;">
            {{ session('warning') }}
        </div>
    @endif

    <div class="siswaGrid">
        @forelse($kelas as $k)
            <div class="siswaCard">
                <div class="siswaTop">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="activityAvatar" style="background:#dbeafe;color:#1d4ed8;">
                            {{ strtoupper(substr((string) $k->nama_kelas, 0, 1)) }}
                        </div>
                        <div>
                            <div class="siswaName">{{ $k->nama_kelas }}</div>
                            <div class="siswaMeta">Total siswa: {{ $k->siswas_count }}</div>
                        </div>
                    </div>
                </div>

                <div class="siswaActions">
                    <a class="smallBtn edit" href="{{ route('admin.kelas.edit', $k) }}">
                        <ion-icon name="create-outline"></ion-icon>
                        Edit
                    </a>

                    <form method="POST" action="{{ route('admin.kelas.destroy', $k) }}" onsubmit="return confirm('Yakin hapus kelas ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="smallBtn delete" style="cursor:pointer;border:1px solid rgba(239,68,68,0.22);background:rgba(239,68,68,0.08);">
                            <ion-icon name="trash-outline"></ion-icon>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="emptyState">Belum ada data kelas.</div>
        @endforelse
    </div>

    <div style="padding:0 16px 30px;">
        {{ $kelas->links() }}
    </div>
@endsection
