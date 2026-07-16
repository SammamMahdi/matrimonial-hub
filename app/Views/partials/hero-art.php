<?php

/**
 * Hero backdrop, drawn rather than photographed.
 *
 * The original hero streamed a 97 MB videoplayback.mp4 and a 28 MB
 * weddingbackground1.mp4 from the repo — 125 MB of binary before the page
 * could paint, and both were third-party footage. This is a few KB of inline
 * SVG: a gradient ground with an alpona-style rosette, the motif drawn on
 * Bengali wedding floors. It scales to any viewport and costs no request.
 */
?>
<svg viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="heroGround" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#4a0f24"/>
            <stop offset="0.45" stop-color="#6d1836"/>
            <stop offset="1" stop-color="#2b0912"/>
        </linearGradient>

        <radialGradient id="heroGlowGold" cx="0.5" cy="0.5" r="0.5">
            <stop offset="0" stop-color="#e0bc72" stop-opacity="0.5"/>
            <stop offset="1" stop-color="#e0bc72" stop-opacity="0"/>
        </radialGradient>

        <radialGradient id="heroGlowRose" cx="0.5" cy="0.5" r="0.5">
            <stop offset="0" stop-color="#cf4670" stop-opacity="0.55"/>
            <stop offset="1" stop-color="#cf4670" stop-opacity="0"/>
        </radialGradient>

        <!-- One petal, rotated 16 times to build the rosette. -->
        <g id="alponaPetal">
            <path d="M0 0 C 26 -46, 26 -104, 0 -150 C -26 -104, -26 -46, 0 0 Z"
                  fill="none" stroke="#e0bc72" stroke-width="1.4"/>
            <circle cx="0" cy="-118" r="4.5" fill="#e0bc72" opacity="0.5"/>
            <path d="M0 -30 C 12 -58, 12 -86, 0 -110 C -12 -86, -12 -58, 0 -30 Z"
                  fill="#e0bc72" opacity="0.12"/>
        </g>

        <filter id="heroSoften" x="-20%" y="-20%" width="140%" height="140%">
            <feGaussianBlur stdDeviation="26"/>
        </filter>
    </defs>

    <rect width="1440" height="900" fill="url(#heroGround)"/>

    <!-- Depth: two soft lights, offset so the composition is not symmetrical -->
    <ellipse cx="290" cy="200" rx="440" ry="360" fill="url(#heroGlowRose)" filter="url(#heroSoften)"/>
    <ellipse cx="1180" cy="720" rx="480" ry="380" fill="url(#heroGlowGold)" filter="url(#heroSoften)"/>

    <!-- Alpona rosette -->
    <g transform="translate(720 450)" opacity="0.5">
        <g>
            <?php for ($i = 0; $i < 16; $i++): ?>
                <use href="#alponaPetal" transform="rotate(<?= $i * 22.5 ?>)"/>
            <?php endfor; ?>
        </g>

        <g transform="scale(0.56)" opacity="0.75">
            <?php for ($i = 0; $i < 16; $i++): ?>
                <use href="#alponaPetal" transform="rotate(<?= $i * 22.5 + 11.25 ?>)"/>
            <?php endfor; ?>
        </g>

        <circle r="196" fill="none" stroke="#e0bc72" stroke-width="1" opacity="0.35"/>
        <circle r="212" fill="none" stroke="#e0bc72" stroke-width="0.6" opacity="0.22"
                stroke-dasharray="2 9"/>
        <circle r="30" fill="none" stroke="#e0bc72" stroke-width="1.2" opacity="0.6"/>
        <circle r="9" fill="#e0bc72" opacity="0.5"/>
    </g>

    <!-- Marigold strings, the way a Bengali wedding stage is dressed -->
    <g opacity="0.28">
        <?php
        $strands = [60, 150, 240, 330, 1110, 1200, 1290, 1380];
        foreach ($strands as $index => $x):
            $length = 150 + (($index * 67) % 190);
            ?>
            <line x1="<?= $x ?>" y1="0" x2="<?= $x ?>" y2="<?= $length ?>"
                  stroke="#e0bc72" stroke-width="0.8" opacity="0.5"/>
            <?php for ($y = 18; $y < $length; $y += 26): ?>
                <circle cx="<?= $x ?>" cy="<?= $y ?>" r="5.5" fill="#e0bc72" opacity="0.5"/>
            <?php endfor; ?>
        <?php endforeach; ?>
    </g>
</svg>
