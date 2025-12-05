<?php
defined('ABSPATH') or die('No script kiddies please!');
$items = $tracks['results'] ?? $tracks;
//echo "<pre>"; print_r($items); echo "</pre>";
?>
<!--<link rel="stylesheet" id="finalsix-css" href="https://use.typekit.net/vpf7cpc.css?ver=6.8.3" media="all">-->
<!--<link rel="preconnect" href="https://fonts.googleapis.com">-->
<!--<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>-->
<!--<link href="https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">-->
<!--<link href="https://fonts.googleapis.com/css?family=Calibri:400,700,400italic,700italic" rel="stylesheet">-->
<style>
    .tailwind-scope .pt-calibri-regular {
        font-family: "Calibri", sans-serif !important;
        font-weight: 400 !important;
        font-style: normal !important;
    }

    .tailwind-scope .pt-sans-regular {
        font-family: "PT Sans", sans-serif !important;
        font-weight: 400 !important;
        font-style: normal !important;
    }

    .tailwind-scope .pt-sans-bold {
        font-family: "PT Sans", sans-serif !important;
        font-weight: 600 !important;
        font-style: normal !important;
        font-size: clamp(1rem, 1rem + ((1vw - 0.2rem) * 0.227), 1.125rem) !important;
    }

    .tailwind-scope .pt-sans-bold-normal {
        font-family: "PT Sans", sans-serif !important;
        font-weight: 600 !important;
        font-style: normal !important;
    }

    .tailwind-scope .pt-sans-regular-italic {
        font-family: "PT Sans", sans-serif !important;
        font-weight: 400 !important;
        font-style: italic !important;
    }

    .tailwind-scope .pt-sans-bold-italic {
        font-family: "PT Sans", sans-serif !important;
        font-weight: 700 !important;
        font-style: italic !important;
    }

    /* TEXT LINK */
    .tailwind-scope .is-text-link {
        text-decoration: none;
        font-style: normal;
        font-weight: 600;
    }

    .tailwind-scope .is-text-link::after {
        content: "\203A";
        font-size: 1.5em;
        line-height: 0;
        margin-left: .5em;
        transition: transform .4s;
        display: inline-block;
        vertical-align: -2px;
    }

    .tailwind-scope .is-text-link:hover::after {
        transform: translate(0.25em, 0);
    }

</style>
<div class="tailwind-scope tw container mx-auto px-4 py-10">

    <?php if (!empty($items)): ?>
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-[#16194F] tk-finalsix">
                Beschikbare Trainingsmomenten
            </h2>

            <p class="mt-3 text-[#000] max-w-2xl mx-auto text-base pt-sans-regular">
                Kies uit verschillende startdata en locaties voor de training <?= $items[0]['module_title'] ?>
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

            <?php foreach ($items as $index => $track):

                // unieke modal ID
                $modal_id = 'modal-' . $track['id'] . '-' . $index;

            ?>

                <div class="bg-white rounded-3xl border border-gray-200 p-8 flex flex-col">

                    <!-- Icon + Titel -->
                    <div class="flex items-center mb-6">

                        <div class="w-16 h-10 flex items-center justify-center rounded-xl
                                    bg-[linear-gradient(90deg,rgb(239,126,47)_0%,rgb(200,52,97)_100%)]
                                    text-white shadow-sm mr-4">

                            <!-- Heroicon: Calendar -->
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7
                                      a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>

                        <h3 class="text-base font-bold text-[#16194F] tk-finalsix leading-tight">
                            <?php echo esc_html($track['module_title']); ?>
                        </h3>
                    </div>

                    <!-- Startdatum -->
                    <div class="flex items-start text-sm mb-3">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-4 w-4 mr-2 text-[#EF7E2F]" fill="currentColor"
                             viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="10"/>
                        </svg>

                        <div class="text-[#211A50] leading-tight pt-calibri-regular">
                            <strong>Start:</strong>
                            <?php echo esc_html($track['training_start']); ?>
                        </div>
                    </div>

                    <!-- Locatie -->
                    <div class="flex text-sm mb-8 items-start">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-4 w-4 mr-2 text-[#C83461]" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19.5 10.5c0 7.125-7.5 11.25-7.5 11.25S4.5 17.625 4.5 10.5A7.5 7.5 0 1119.5 10.5z"/>
                        </svg>

                        <div class="text-[#211A50] leading-tight pt-calibri-regular">
                            <strong>Locatie:</strong>
                            <?php echo esc_html($track['traininglessons'][0]['location_label_no_room'] ?? 'n.n.b.'); ?>
                        </div>
                    </div>

                    <!-- Bekijk trainingsdagen -->
                    <button class="open-modal-btn w-full text-[#C83461] hover:text-[#9f264b] pt-sans-bold is-text-link rounded-xl py-3 mb-6 flex items-center justify-center transition cursor-pointer"
                            data-modal-id="<?php echo $modal_id; ?>">

<!--                        <svg xmlns="http://www.w3.org/2000/svg"-->
<!--                             class="h-5 w-5 mr-2 text-[#C83461]" fill="none"-->
<!--                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round"-->
<!--                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round"-->
<!--                                  d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943-->
<!--                                     9.542 7c-1.274 4.057-5.065 7-9.542 7-->
<!--                                     -4.477 0-8.268-2.943-9.542-7z"/>-->
<!--                        </svg>-->

                        Bekijk <?php echo count($track['traininglessons']); ?> trainingsdagen
                    </button>

                    <!-- Aanmelden -->
                    <a href="<?php echo esc_url($track['link_to_mentor'] ?? '#'); ?>"
                       class="w-full block text-center py-3 text-white rounded-full pt-sans-bold-normal is-text-link pt-sans-bold
                              bg-[linear-gradient(90deg,rgb(239,126,47)_0%,rgb(200,52,97)_100%)]
                              hover:opacity-90 transition shadow-md">
                        Meld je aan
                    </a>

                </div>


                <!-- MODAL -->
                <div id="<?php echo $modal_id; ?>"
                     class="modal-backdrop hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                     data-modal-id="<?php echo $modal_id; ?>">

                    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative max-h-[80vh] overflow-y-auto"
                            onclick="event.stopPropagation()">

                        <!-- Sluitknop -->
                        <button
                                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl"
                                onclick="document.getElementById('<?php echo $modal_id; ?>').classList.add('hidden')">
                            ✕
                        </button>

                        <h3 class="text-xxl tk-finalsix font-bold mb-4 text-[#211a50]">Trainingsdagen</h3>

                        <ul class="space-y-3 text-[#211A50] text-sm pt-sans-regular list-none pl-0">
                            <?php foreach ($track['traininglessons'] as $lesson): ?>
                                <li class="p-4 bg-[#E0DFEC] rounded-xl">

                                    <div class="font-semibold mb-1 tk-finalsix text-[#211a50]">
                                        <?php echo esc_html($lesson['lesson']); ?>
                                    </div>

                                    <div class="flex items-center text-xs mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-4 w-4 mr-1 text-[#EF7E2F]" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7
                                     a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span><?php echo esc_html($lesson['start']); ?></span>
                                    </div>
                                    <div class="text-[#211A50] flex items-center text-xs mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-4 w-4 mr-1 text-[#EF7E2F]"
                                             viewBox="0 0 24 24"
                                             width="24" height="24">
                                            <circle cx="12" cy="12" r="9"
                                                    fill="none"
                                                    stroke="#c83461"
                                                    stroke-width="2"/>
                                            <line x1="12" y1="12" x2="12" y2="7"
                                                  stroke="#c83461"
                                                  stroke-width="2"
                                                  stroke-linecap="round"/>
                                            <line x1="12" y1="12" x2="16" y2="12"
                                                  stroke="#c83461"
                                                  stroke-width="2"
                                                  stroke-linecap="round"/>
                                        </svg>
                                        <span class="text-[#211A50]"><?php echo esc_html($lesson['start_time']); ?> - <?php echo esc_html($lesson['end_time']); ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <!--
                        <button
                                onclick="document.getElementById('<?php echo $modal_id; ?>').classList.add('hidden')"
                                class="mt-6 pt-sans-bold-normal w-full py-3 text-center rounded-full bg-gray-100 hover:bg-gray-200 transition text-sm">
                            Sluiten
                        </button>
                        -->
                        <button
                                onclick="document.getElementById('<?php echo $modal_id; ?>').classList.add('hidden')"
                                class="mt-6 pt-sans-bold-normal w-full py-3 text-white text-center rounded-full is-text-link cursor-pointer bg-[linear-gradient(90deg,rgb(239,126,47)_0%,rgb(200,52,97)_100%)] transition text-base">
                            Sluiten
                        </button>

                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    <?php endif; ?>

</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // OPEN
        document.querySelectorAll(".open-modal-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.dataset.modalId;
                const modal = document.getElementById(id);
                if (modal) modal.classList.remove("hidden");
            });
        });

        // CLOSE: op de sluit-knop (✕)
        document.querySelectorAll(".modal-backdrop .close-modal").forEach(btn => {
            btn.addEventListener("click", function() {
                this.closest(".modal-backdrop").classList.add("hidden");
            });
        });

        // CLOSE: klik buiten de modal
        document.querySelectorAll(".modal-backdrop").forEach(backdrop => {
            backdrop.addEventListener("click", function(e) {
                if (e.target === this) {
                    this.classList.add("hidden");
                }
            });
        });

        // CLOSE: ESC key
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                document.querySelectorAll(".modal-backdrop").forEach(m => m.classList.add("hidden"));
            }
        });

    });
</script>


