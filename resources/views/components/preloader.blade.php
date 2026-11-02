{{--
    ╔═════════════════════════════════════════════════════════════════╗
    ║  Jessi's ClassiQ Store — White Luxury Editorial Preloader       ║
    ║  Explicit inline z-index & positioning: Guaranteed on TOP of   ║
    ║  every sticky header, fixed bar, and page element.              ║
    ╚═════════════════════════════════════════════════════════════════╝
--}}
<div
    id="jc-preloader"
    role="status"
    aria-label="Loading Jessi's ClassiQ Store"
    style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; z-index: 2147483647 !important; background-color: #ffffff; overflow: hidden; pointer-events: auto; user-select: none;"
>
    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MAIN WHITE LUXURY CANVAS (z-index: 10 inside preloader)     --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div
        id="jc-canvas"
        style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; z-index: 10; display: flex; flex-direction: column; justify-content: space-between; background-color: #ffffff;"
    >
        {{-- Subtle ambient rose warmth at the top --}}
        <div style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 650px; max-width: 100%; height: 260px; background: radial-gradient(ellipse at top, rgba(252, 232, 238, 0.85) 0%, rgba(253, 242, 244, 0.3) 60%, transparent 100%); pointer-events: none; filter: blur(40px);"></div>

        {{-- ── Top Navigation Bar ── --}}
        <div
            id="jc-topbar"
            style="position: relative; z-index: 2; display: flex; align-items: center; justify-content: space-between; padding: 24px 32px 16px 32px;"
        >
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 9999px; background-color: #e06383;"></span>
                <span style="font-family: 'Plus Jakarta Sans', -apple-system, sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3em; color: rgba(17, 17, 17, 0.5);">
                    Official Boutique
                </span>
            </div>

            <button
                type="button"
                id="jc-skip-btn"
                style="padding: 6px 18px; border-radius: 9999px; font-family: 'Plus Jakarta Sans', -apple-system, sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.2em; color: rgba(17, 17, 17, 0.55); border: 1px solid rgba(0, 0, 0, 0.12); background-color: transparent; cursor: pointer; transition: all 150ms ease-out;"
                onmouseover="this.style.borderColor='rgba(0,0,0,0.3)'; this.style.color='#111111'; this.style.backgroundColor='rgba(0,0,0,0.02)';"
                onmouseout="this.style.borderColor='rgba(0,0,0,0.12)'; this.style.color='rgba(17,17,17,0.55)'; this.style.backgroundColor='transparent';"
            >
                Skip &rarr;
            </button>
        </div>

        {{-- ── Center Editorial Content ── --}}
        <div style="position: relative; z-index: 2; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0 24px; margin-top: -16px;">
            <div
                id="jc-center-box"
                style="width: 100%; max-width: 580px; text-align: center;"
            >

                {{-- Typing Headline (High contrast black, large, crystal clear) --}}
                <div style="min-height: 58px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                    <h1
                        style="margin: 0; font-family: 'Syne', 'Playfair Display', -apple-system, BlinkMacSystemFont, sans-serif; font-weight: 800; font-size: clamp(1.6rem, 4vw, 2.75rem); letter-spacing: -0.02em; line-height: 1.15; color: #111111; user-select: text;"
                    >
                        <span id="jc-typed-title" style="color: #111111;"></span><span
                            id="jc-cursor"
                            style="display: inline-block; width: 3.5px; height: 0.82em; background-color: #e06383; margin-left: 6px; border-radius: 2px; vertical-align: middle; box-shadow: 0 0 10px rgba(224, 99, 131, 0.7); animation: jc-cursor-blink 0.75s ease-in-out infinite;"
                        ></span>
                    </h1>
                </div>

                {{-- Typing Subtitle --}}
                <div style="min-height: 24px; margin-bottom: 36px; display: flex; align-items: center; justify-content: center;">
                    <p
                        style="margin: 0; font-family: 'Plus Jakarta Sans', -apple-system, sans-serif; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.28em; color: rgba(17, 17, 17, 0.55);"
                    >
                        <span id="jc-typed-sub"></span>
                    </p>
                </div>

                {{-- Progress Track --}}
                <div style="max-width: 290px; margin: 0 auto; display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 10px; text-transform: uppercase; letter-spacing: 0.15em; color: rgba(17, 17, 17, 0.5);">
                        <span id="jc-status-text">Preparing Boutique</span>
                        <span id="jc-progress-percent" style="font-weight: 700; color: rgba(17, 17, 17, 0.75);">0%</span>
                    </div>
                    <div style="width: 100%; height: 3.5px; background-color: rgba(0, 0, 0, 0.08); border-radius: 9999px; overflow: hidden;">
                        <div
                            id="jc-progress-bar"
                            style="height: 100%; width: 0%; border-radius: 9999px; background: linear-gradient(to right, #111111, #ca4367, #e06383); transition: width 75ms ease-out;"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Bottom Editorial Bar ── --}}
        <div
            id="jc-bottombar"
            style="position: relative; z-index: 2; display: flex; align-items: center; justify-content: space-between; padding: 16px 32px 24px 32px;"
        >
            <span style="font-family: ui-monospace, monospace; font-size: 9px; text-transform: uppercase; letter-spacing: 0.18em; color: rgba(17, 17, 17, 0.35);">Edition // SS-2026</span>
            <span style="font-family: ui-monospace, monospace; font-size: 9px; text-transform: uppercase; letter-spacing: 0.18em; color: rgba(17, 17, 17, 0.35);">Paystack Secured &bull; Verified</span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- CASCADING PINK VEIL EXIT (Layered deep to light)            --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}

    {{-- Veil 1 — Velvet wine (z-index: 1) --}}
    <div
        id="jc-veil-1"
        style="position: absolute; inset: 0; z-index: 1; pointer-events: none; background: linear-gradient(to top right, #772a3e, #8e2d46, #ab3252); opacity: 1;"
    >
        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; opacity: 0.07; overflow: hidden; pointer-events: none;">
            <span style="font-family: 'Syne', sans-serif; font-weight: 900; font-size: 24vw; color: white; letter-spacing: -0.05em; line-height: 1;">JC</span>
        </div>
    </div>

    {{-- Veil 2 — Deep raspberry (z-index: 2) --}}
    <div
        id="jc-veil-2"
        style="position: absolute; inset: 0; z-index: 2; pointer-events: none; background: linear-gradient(to bottom left, #ab3252, #ca4367, #e06383); opacity: 1;"
    ></div>

    {{-- Veil 3 — Signature rose (z-index: 3) --}}
    <div
        id="jc-veil-3"
        style="position: absolute; inset: 0; z-index: 3; pointer-events: none; background: linear-gradient(to right, #ca4367, #e06383, #e57a94); opacity: 1;"
    ></div>

    {{-- Veil 4 — Peony blush (z-index: 4) --}}
    <div
        id="jc-veil-4"
        style="position: absolute; inset: 0; z-index: 4; pointer-events: none; background: linear-gradient(to top right, #e57a94, #f0aab9, #f7d0d9); opacity: 1;"
    ></div>

    {{-- Veil 5 — Champagne silk (z-index: 5) --}}
    <div
        id="jc-veil-5"
        style="position: absolute; inset: 0; z-index: 5; pointer-events: none; background: linear-gradient(to bottom, #fdf2f4, #fbe6eb, #ffffff); opacity: 1;"
    ></div>
</div>

<style>
@keyframes jc-cursor-blink {
    0%, 45%, 100% { opacity: 1; }
    50%, 95%      { opacity: 0; }
}
</style>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- PURE VANILLA JS: Guaranteed execution, zero race conditions     --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<script>
(function() {
    const preloader   = document.getElementById('jc-preloader');
    if (!preloader) return;

    const canvas      = document.getElementById('jc-canvas');
    const titleEl     = document.getElementById('jc-typed-title');
    const subEl       = document.getElementById('jc-typed-sub');
    const statusEl    = document.getElementById('jc-status-text');
    const progressEl  = document.getElementById('jc-progress-percent');
    const progressBar = document.getElementById('jc-progress-bar');
    const skipBtn     = document.getElementById('jc-skip-btn');

    const veil1       = document.getElementById('jc-veil-1');
    const veil2       = document.getElementById('jc-veil-2');
    const veil3       = document.getElementById('jc-veil-3');
    const veil4       = document.getElementById('jc-veil-4');
    const veil5       = document.getElementById('jc-veil-5');

    const TITLE = "Jessi's ClassiQ Store";
    const SUB   = "WEST OF MINES, JOS  \u2022  HAUTE COUTURE & LEATHER";

    let titleDone = false;
    let progressDone = false;
    let isExiting = false;
    let isSkipped = false;
    let progress = 0;

    // ── Function: Exit Preloader with Cascading Pink Veils ──
    function triggerExit() {
        if (isExiting) return;
        isExiting = true;

        if (isSkipped) {
            preloader.style.transition = 'opacity 140ms ease-out';
            preloader.style.opacity = '0';
            setTimeout(function() {
                if (preloader.parentNode) preloader.parentNode.removeChild(preloader);
            }, 150);
            return;
        }

        // 1. Fade out the white canvas smoothly to reveal the veils
        if (canvas) {
            canvas.style.transition = 'opacity 220ms ease-out, transform 220ms ease-out';
            canvas.style.opacity = '0';
            canvas.style.transform = 'scale(0.99)';
        }

        // 2. Cascade veils from Veil 5 (champagne silk) down to Veil 1 (velvet wine)
        if (veil5) {
            setTimeout(function() {
                veil5.style.transition = 'opacity 220ms ease-out, transform 220ms ease-out';
                veil5.style.opacity = '0';
                veil5.style.transform = 'scale(1.02)';
            }, 30);
        }

        if (veil4) {
            setTimeout(function() {
                veil4.style.transition = 'opacity 240ms ease-out, transform 240ms ease-out';
                veil4.style.opacity = '0';
                veil4.style.transform = 'scale(1.02)';
            }, 100);
        }

        if (veil3) {
            setTimeout(function() {
                veil3.style.transition = 'opacity 260ms ease-out, transform 260ms ease-out';
                veil3.style.opacity = '0';
                veil3.style.transform = 'scale(1.02)';
            }, 170);
        }

        if (veil2) {
            setTimeout(function() {
                veil2.style.transition = 'opacity 280ms ease-out, transform 280ms ease-out';
                veil2.style.opacity = '0';
                veil2.style.transform = 'scale(1.03)';
            }, 240);
        }

        if (veil1) {
            setTimeout(function() {
                veil1.style.transition = 'opacity 300ms ease-out, transform 300ms ease-out';
                veil1.style.opacity = '0';
                veil1.style.transform = 'scale(1.04)';
            }, 310);
        }

        // 3. Final DOM removal
        setTimeout(function() {
            if (preloader.parentNode) {
                preloader.parentNode.removeChild(preloader);
            }
        }, 650);
    }

    // ── Function: Instant Skip ──
    function skip() {
        if (isExiting) return;
        isSkipped = true;
        triggerExit();
    }

    if (skipBtn) {
        skipBtn.addEventListener('click', skip);
    }

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') skip();
    });

    function checkReady() {
        if (titleDone && progressDone) {
            setTimeout(triggerExit, 320);
        }
    }

    // ── Phase 1: Typing Title ──
    let titleIdx = 0;
    function typeTitle() {
        if (isExiting) return;
        if (titleIdx < TITLE.length) {
            titleEl.textContent += TITLE.charAt(titleIdx++);
            setTimeout(typeTitle, 45);
        } else {
            // Title complete -> start subtitle
            setTimeout(typeSubtitle, 80);
        }
    }

    // ── Phase 2: Typing Subtitle ──
    let subIdx = 0;
    function typeSubtitle() {
        if (isExiting) return;
        if (subIdx < SUB.length) {
            subEl.textContent += SUB.charAt(subIdx++);
            setTimeout(typeSubtitle, 15);
        } else {
            titleDone = true;
            checkReady();
        }
    }

    // Start typing after initial 100ms
    setTimeout(typeTitle, 100);

    // ── Phase 3: Progress Bar (~1.7s total) ──
    const progressTimer = setInterval(function() {
        if (isExiting) {
            clearInterval(progressTimer);
            return;
        }

        if (progress < 100) {
            progress = Math.min(100, progress + Math.floor(Math.random() * 3) + 2);
            if (progressBar) progressBar.style.width = progress + '%';
            if (progressEl)  progressEl.textContent = progress + '%';

            if (progress > 30 && progress < 70 && statusEl) {
                statusEl.textContent = 'Curating Collection';
            } else if (progress >= 70 && progress < 100 && statusEl) {
                statusEl.textContent = 'Finishing Touches';
            }

            if (progress >= 100) {
                if (statusEl) statusEl.textContent = 'Welcome to Jessi\'s ClassiQ';
                progressDone = true;
                clearInterval(progressTimer);
                checkReady();
            }
        }
    }, 45);

    // ── Safety Fallback: Never stay stuck ──
    setTimeout(function() {
        if (!isExiting) {
            titleDone = true;
            progressDone = true;
            triggerExit();
        }
    }, 3200);
})();
</script>
