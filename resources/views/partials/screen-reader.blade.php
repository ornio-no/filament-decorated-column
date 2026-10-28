{{-- The screen-reader attributes for a decoration's root element: hidden when decorative, otherwise an image named by its label. Include it inside the tag, passing ['decorative' => $isDecorative(), 'label' => $getLabel()]. --}}
@if ($decorative)
    aria-hidden="true"
@else
    role="img"
    aria-label="{{ $label }}"
@endif
