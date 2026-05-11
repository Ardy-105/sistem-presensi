@extends('layout.admin')

@section('content')

    <div class="pageHeaderRow">
        <h2>Data Siswa</h2>
    </div>

    @if (session('success'))
        <div class="errorList">
            {{ session('success') }}
        </div>
    @endif

    <div class="siswaGrid">
        @forelse($siswas as $siswa)
            <div class="siswaCard">
                <div class="siswaTop">
                    <div style="display:flex;align-items:center;gap:12px;min-width:0;">
                        @php
                            $initial = strtoupper(substr((string) ($siswa->nama_siswa ?? ''), 0, 1));
                        @endphp
                        <div class="activityAvatar" class="activityAvatar">
                            {{ $initial }}
                        </div>
                        <div style="min-width:0;">
                            <div class="siswaName">{{ $siswa->nama_siswa }}</div>
                            <div class="siswaMeta">
                                NIS: {{ $siswa->nis }} • Kelas: {{ $siswa->relKelas->nama_kelas ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="siswaActions">
                    <a class="smallBtn edit" href="{{ route('admin.siswa.edit', $siswa) }}">
                        <ion-icon name="create-outline"></ion-icon>
                        Edit
                    </a>

                    <form method="POST" action="{{ route('admin.siswa.destroy', $siswa) }}" onsubmit="return confirm('Yakin hapus siswa ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="smallBtn delete" style="cursor:pointer;">
                            <ion-icon name="trash-outline"></ion-icon>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="emptyState">Belum ada data siswa.</div>
        @endforelse
    </div>

    <div style="padding:0 16px 30px;">
        {{ $siswas->links() }}
    </div>

    <a href="{{ route('admin.siswa.create') }}" class="fabAdd" aria-label="Tambah Siswa">
        <ion-icon name="add-outline"></ion-icon>
    </a>
@endsection

