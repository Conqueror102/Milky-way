/**
 * Scroll reveal.
 *
 * Markup opts in with data-reveal="up|down|left|right|scale|zoom|fade|clip|pop|lines|trigger".
 * Elements stay visible unless <html> has .motion-ready, which the inline guard in the
 * marketing layout only sets when the visitor has not asked for reduced motion.
 */
const root = document.documentElement;
const supported = 'IntersectionObserver' in window && root.classList.contains('motion-ready');

if (!supported) {
    root.classList.remove('motion-ready');
} else {
    /** Split a heading into words that can rise out of a mask. */
    const splitWords = (el) => {
        let index = 0;
        const fragment = document.createDocumentFragment();

        const wrap = (node) => {
            const outer = document.createElement('span');
            const inner = document.createElement('span');
            outer.className = 'w';
            outer.setAttribute('aria-hidden', 'true');
            inner.className = 'wi';
            inner.style.setProperty('--wi', String(index++));
            inner.appendChild(node);
            outer.appendChild(inner);

            return outer;
        };

        el.setAttribute('aria-label', el.textContent.replace(/\s+/g, ' ').trim());

        for (const node of [...el.childNodes]) {
            if (node.nodeType === Node.TEXT_NODE) {
                for (const part of node.textContent.split(/([ \t\r\n]+)/)) {
                    if (part === '') continue;
                    fragment.appendChild(/^[ \t\r\n]+$/.test(part) ? document.createTextNode(' ') : wrap(document.createTextNode(part)));
                }
            } else if (node.nodeName === 'BR') {
                fragment.appendChild(node);
            } else {
                fragment.appendChild(wrap(node));
            }
        }

        el.replaceChildren(fragment);
    };

    document.querySelectorAll('[data-reveal="lines"]').forEach(splitWords);

    // Children of a [data-stagger] group share out their delays in document order.
    document.querySelectorAll('[data-stagger]').forEach((group) => {
        const step = Number(group.dataset.stagger) || 90;
        const from = Number(group.dataset.staggerFrom) || 0;

        group.querySelectorAll('[data-reveal]').forEach((el, i) => {
            el.style.setProperty('--d', String(from + i * step));
        });
    });

    /**
     * A clip-revealed element is collapsed to zero height, and the observer sees nothing to
     * intersect in something with no area, so it would never fire. Watch its container
     * instead (skipping <picture>, which has no box of its own) and reveal it from there.
     */
    const watchedFor = (el) => {
        if (el.dataset.reveal !== 'clip') return el;

        let host = el.parentElement;
        if (host && host.tagName === 'PICTURE') host = host.parentElement;

        return host || el;
    };

    const reveal = (el) => {
        el.classList.add('is-in');

        // Once it has landed, hand the element back to its own styles so hover and focus
        // transitions defined on it work again.
        const delay = Number(el.style.getPropertyValue('--d')) || 0;
        const settle = 1800 + delay + (el.dataset.reveal === 'lines' ? 1400 : 0);
        setTimeout(() => el.classList.add('is-done'), settle);
    };

    const watched = new Map();

    const observer = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (!entry.isIntersecting) continue;

                observer.unobserve(entry.target);
                (watched.get(entry.target) || []).forEach(reveal);
            }
        },
        { threshold: 0.15, rootMargin: '0px 0px -8% 0px' },
    );

    document.querySelectorAll('[data-reveal]').forEach((el) => {
        const host = watchedFor(el);

        if (!watched.has(host)) watched.set(host, []);
        watched.get(host).push(el);
        observer.observe(host);
    });

    window.__revealReady = true;
}
