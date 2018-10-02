<?php

    if (session_id()==='') session_start();

    /*****************/

    echo '
        <div class="content-left flt-left">

            <p class="section-title">Suština "sustava poboljšavanja nastave"</p>

            <ul>
                <li>
                    <span>Sve ocjene se gledaju na sve prostore/izvođaće/sadržaje kao cijelina zbog razloga demonstriranog u nastavku:</span>
                    <ul>
                        <li>Ako je ukupna ocjena 3 i sve pojedinačno se smatra da je za 5,</li>
                        <li>onda studenti lažno ocjenjuju ili pojedinačno stvarno nije za 5,</li>
                        <li>pa ako se smatra da studenti lažno ocijenjuje te da je sve za 5,</li>
                        <li>odlučilo se da se neće poboljšavati nastava, ne treba sustav.</li>
                    </ul>
                </li>
            </ul>

        </div>
    ';

?>