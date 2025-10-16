@php
	$candidates = [
		public_path('images/etio-logo.png') => asset('images/etio-logo.png'),
		public_path('images/etio-logo.jpg') => asset('images/etio-logo.jpg'),
		public_path('images/etio-logo.jpeg') => asset('images/etio-logo.jpeg'),
		public_path('images/etio-logo.webp') => asset('images/etio-logo.webp'),
		public_path('images/etio-logo.svg') => asset('images/etio-logo.svg'),
	];
	// Default fallback (will be overridden if a candidate exists)
	$src = asset('images/etio-logo.png');
	foreach ($candidates as $path => $url) {
		if (file_exists($path)) { $src = $url; break; }
	}
@endphp
<img src="{{ $src }}" alt="Etiopathie - Logo" {{ $attributes->merge(['class' => 'w-16 h-16 sm:w-16 sm:h-16 object-contain']) }} />




