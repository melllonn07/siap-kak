<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAP-KAK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('images/logo-bps.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .external-event {
            padding: 8px 16px;
            margin-bottom: 10px;
            background: #296374;
            color: white;
            border-radius: 6px;
            cursor: grab;
            font-size: 0.875rem;
            line-height: 1.25rem;
            transition: background-color .2s ease;
        }

        .external-event:hover {
            background: #34788C;
        }

        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .modal-backdrop-custom {
            background: rgba(0, 0, 0, 0.4);
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen">
    <div class="max-w-375 mx-auto py-6 px-4">
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="w-full lg:w-1/4">
                <div class="bg-white rounded-xl shadow p-5 mb-4">
                    <div class="flex gap-2">
                        <div class="flex items-start gap-3">
                            <img src="{{ asset('images/logo-bps.png') }}" alt="Logo BPS" class="w-10 h-10 object-contain mt-1">

                            <div>
                                <h1 class="text-2xl font-bold mb-1">SIAP-KAK</h1>
                                <p class="text-sm text-gray-600">Sistem Informasi Pengingat Kegiatan Kantor By BPS Kabupaten Muara Enim</p>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" 
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                               bg-red-50 text-red-500 hover:bg-red-100 transition-colors duration-200">
                            <i class="fa-solid fa-right-from-bracket"></i>Logout
                            </button>
                        </form>
                    </div>


                    <div class="mt-4 flex flex-wrap gap-2">
                        <a class="hidden" href="{{ route('employees.index') }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">
                            Data Pegawai
                        </a>
                        <a href="{{ route('activity-templates.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-[#296374] hover:bg-[#34788C] text-white rounded-lg text-sm font-medium transition-all duration-200">
                            <i class="fa-solid fa-list"></i>
                            Form Kegiatan
                        </a>
                        <a href="{{ route('whatsapp-groups.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-[#0C2C55] hover:bg-[#15427A] text-white rounded-lg text-sm font-medium transition-all duration-200">
                            <i class="fa-solid fa-users-between-lines"></i>
                            Grup WhatsApp
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-5">
                    <h2 class="text-lg font-semibold mb-3">Daftar Template Kegiatan</h2>
                    <p class="text-sm text-gray-500 mb-4">Bisa didrag ke kalender.</p>

                    <div id="external-events">
                        @forelse ($activityTemplates as $template)
                            <div class="external-event" data-template-id="{{ $template->id }}"
                                data-template-name="{{ $template->name }}">
                                {{ $template->name }}
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada template kegiatan.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-3/4">
                <div class="bg-white rounded-xl shadow p-5">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="eventModal" class="fixed inset-0 hidden items-center justify-center z-50">
        <div class="absolute inset-0 modal-backdrop-custom"></div>

        <div class="relative bg-white w-full max-w-2xl rounded-xl shadow-xl z-10 max-h-[90vh] flex flex-col px-4">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 id="modalTitle" class="text-xl font-bold">Tambah Event</h2>
                <button type="button" id="closeModalBtn" class="text-gray-500 text-2xl leading-none">&times;</button>
            </div>

            <form id="eventForm" class="flex flex-col flex-1 min-h-0 ">
                <input type="hidden" id="event_id" name="event_id">

                <div class="flex-1 overflow-y-auto px-6 py-4 pb-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kegiatan</label>
                        <select id="activity_template_id" name="activity_template_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                            <option value="">Pilih kegiatan</option>
                            @foreach ($activityTemplates as $template)
                                <option value="{{ $template->id }}" data-message="{{ $template->whatsapp_message }}">
                                {{ $template->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea id="notes" name="notes" rows="3"
                            class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Event</label>
                        <input type="text" id="title" name="title"
                            class="w-full border border-gray-300 rounded-md px-3 py-2"
                            placeholder="Kosongkan jika sama dengan nama kegiatan">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" id="event_date" name="event_date"
                            class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                    </div>

                    <div class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Kegiatan</label>
                        <input type="time" id="start_time" name="start_time"
                            class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Kirim WhatsApp</label>
                        <input type="datetime-local" id="send_at" name="send_at"
                            class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <p class="text-sm text-gray-500 mt-1">Kosongkan jika belum ingin dijadwalkan.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status WhatsApp</label>
                        <input type="text" id="whatsapp_status"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Error WhatsApp</label>
                        <textarea id="whatsapp_error_message"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100" rows="2"
                            readonly></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mode Kirim</label>
                        <select id="send_mode" name="send_mode"
                            class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="groups">Ke Grup</option>
                            <option class="hidden" value="employees">Ke Pegawai</option>
                            <option class="hidden" value="both">Ke Pegawai dan Grup</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Grup WhatsApp</label>
                        <select id="whatsapp_group_ids" name="whatsapp_group_ids[]"
                            class="w-full border border-gray-300 rounded-md px-3 py-2" multiple size="4">
                            @foreach ($whatsappGroups as $group)
                                <option value="{{ $group->id }}">
                                    {{ $group->name }} - {{ $group->group_id }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Tekan Ctrl atau Cmd untuk memilih lebih dari satu grup.
                        </p>
                    </div>

                    <div class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Pegawai</label>
                        <select id="employee_ids" name="employee_ids[]"
                            class="w-full border border-gray-300 rounded-md px-3 py-2" multiple size="5">
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->name }}{{ $employee->phone ? ' - ' . $employee->phone : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Tekan Ctrl atau Cmd untuk memilih lebih dari satu pegawai.
                        </p>
                    </div>
                </div>


                <div
                    class="flex flex-wrap justify-between gap-2 px-6 py-4 border-t bg-white sticky bottom-0 shadow-[0_-2px_8px_rgba(0,0,0,0.05)]">
                    <button type="button" id="deleteEventBtn" 
                        class="items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                           bg-red-600 text-white hover:bg-red-700 transition-all duration-200 hidden">
                        Hapus
                    </button>

                    <div class="flex gap-2 ml-auto">
                        <button type="button" id="cancelBtn" 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                               bg-gray-500 text-white hover:bg-gray-600 transition-all duration-200">
                            Batal
                        </button>
                        <button type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                               bg-blue-600 text-white hover:bg-blue-700 transition-all duration-200">
                            Simpan
                        </button>
                        <button type="button" id="sendWhatsappBtn"
                            class="items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                               bg-green-600 text-white hover:bg-green-700 transition-all duration-200 hidden">
                            Kirim WA Sekarang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>