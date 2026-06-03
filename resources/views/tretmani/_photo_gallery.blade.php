@php
    $photos = collect($photos ?? []);
    $photosByType = $photos->groupBy('tip_fotografije');
    $groups = [
        'pre' => 'Pre tretmana',
        'posle' => 'Posle tretmana',
    ];
@endphp

<div class="photo-compare-grid">
    @foreach($groups as $type => $title)
        @php($items = $photosByType->get($type, collect()))
        <section class="photo-section">
            <div class="photo-section__header">
                <h3>{{ $title }}</h3>
                <span>{{ $items->count() }}</span>
            </div>

            @if($items->isEmpty())
                <p class="photo-empty">Nema dodatih fotografija.</p>
            @else
                <div class="photo-grid">
                    @foreach($items as $foto)
                        <a
                            class="photo-card"
                            href="{{ route('tretmani.fotografije.show', ['tretman' => $foto->evidencija_tretmana_id, 'fotografija' => $foto]) }}"
                            target="_blank"
                            rel="noopener"
                        >
                            <img
                                src="{{ route('tretmani.fotografije.show', ['tretman' => $foto->evidencija_tretmana_id, 'fotografija' => $foto]) }}"
                                alt="{{ $foto->naziv }}"
                            >
                            <span class="photo-card__body">
                                <span class="photo-card__name">{{ $foto->naziv }}</span>
                                <span class="photo-card__date">{{ $foto->datum_dodavanja?->format('d.m.Y H:i') }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    @endforeach
</div>
