<x-mail::message>
# Halo {{ $business->name }},

Ini adalah pengingat bahwa masa aktif langganan paket **{{ $plan->name }}** Anda @if($daysRemaining > 0) akan berakhir dalam **{{ $daysRemaining }} hari** @else telah **berakhir** @endif pada tanggal **{{ $expiredAt }}**.

Agar layanan tetap berjalan lancar tanpa hambatan, silakan lakukan perpanjangan langganan sekarang juga. Anda bisa meninjau detail outlet yang akan diperpanjang pada halaman checkout.

<x-mail::button :url="$renewUrl" color="primary">
Perpanjang Langganan Sekarang
</x-mail::button>

Jika Anda memiliki pertanyaan lebih lanjut, jangan ragu untuk menghubungi tim dukungan kami.

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
