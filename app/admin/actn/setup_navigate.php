<?php

echo '
    <section class="content-right flt-right">

        <p class="section-title">Navigacija admin korisničkog sučelja</p>

        <ul class="setup-navigator-side">
            <li>
                <a href="/app/admin/index.php">Početak podešavanja anketnog razdoblja</a>
                <ul>
                    <li>Pokretanje podešavanja novog anketnog razdoblja</li>
                    <li>Zatvaranje pokrenutog anketnog razdoblja</li>
                </ul>
            </li>
            <li>
                <a href="/app/admin/func/setup_course.php">Podešavanje kolegija institucije</a>
                <ul>
                    <li>Dodavanje novog kolegija na popis</li>
                    <li>Uređivanje statusa svakog kolegija sa popisa</li>
                    <li>Uređivanje podataka pojedinog kolegija sa popisa</li>
                </ul>
            </li>
            <li>
                <a href="/app/admin/func/setup_human.php">Podešavanje nastavnika/studenata institucije</a>
                <ul>
                    <li>Dodavanje novog nastavnika/studenta na popis</li>
                    <li>Uređivanje statusa svakog nastavnika/studenta sa popisa</li>
                    <li>Uređivanje podataka pojedinog nastavnika/studenta sa popisa</li>
                    <li>
                        <span>Uređivanje računa pojedinog nastavnika/studenta sa popisa</span>
                        <ul>
                            <li>Izrađivanje novog računa odabranog nastavnika/studenta</li>
                            <li>Uređivanje statusa upotrijebljivosti računa odabranog nastavnika/studenta</li>
                        </ul>
                    </li>
                    <li>
                        <span>Uređivanje kolegija pojedinog nastavnika/studenta sa popisa</span>
                        <ul>
                            <li>Pridruživanje postojećeg kolegija odabranom nastavniku/studentu</li>
                            <li>Uređivanje statusa pridruženosti kolegija odabranom nastavniku/studentu</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>
                <a href="/app/admin/func/setup_system.php">Završetak podešavanja anketnog razdoblja</a>
                <ul>
                    <li>Pokretanje novog anketnog razdoblja</li>
                </ul>
            </li>
        </ul>

    </section>
';

?>