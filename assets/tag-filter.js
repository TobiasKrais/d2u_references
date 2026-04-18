document.addEventListener('click', function (event) {
    var target = event.target;
    var filterLink;
    var tag;

    if (target && target.nodeType !== 1) {
        target = target.parentElement;
    }

    if (!target) {
        return;
    }

    filterLink = target.closest('[data-d2u-reference-filter-tag]');

    if (!filterLink) {
        return;
    }

    tag = filterLink.getAttribute('data-d2u-reference-filter-tag');
    if (!tag) {
        return;
    }

    event.preventDefault();

    var root = filterLink.closest('[data-d2u-reference-filter-root]');
    if (!root) {
        return;
    }

    root.querySelectorAll('[data-d2u-reference-filter-nav] li.active').forEach(function (item) {
        item.classList.remove('active');
    });

    var activeItem = filterLink.closest('li');
    if (activeItem) {
        activeItem.classList.add('active');
    }

    root.querySelectorAll('[data-d2u-reference-filter-item]').forEach(function (item) {
        var tags = (item.getAttribute('data-d2u-reference-filter-tags') || '')
            .split(',')
            .filter(Boolean);
        var isVisible = tag === 'all' || tags.indexOf(tag) !== -1;

        item.hidden = !isVisible;
        item.classList.toggle('d-none', !isVisible);
    });

    root.querySelectorAll('[data-d2u-reference-filter-year-group]').forEach(function (group) {
        var hasVisibleItems = Array.from(group.querySelectorAll('[data-d2u-reference-filter-item]')).some(function (item) {
            return !item.hidden;
        });

        group.hidden = !hasVisibleItems;
        group.classList.toggle('d-none', !hasVisibleItems);
    });
});
