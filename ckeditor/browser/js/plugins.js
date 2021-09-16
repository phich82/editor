/**************************** DATATABLE ****************************/
// Only asc sort by numeric [rule name: only-numeric]
$.fn.dataTableExt.oSort['only-numeric-asc'] = function (prev, next) {
    prev = String(prev).replace(/<[\s\S]*?>/g, '');
    next = String(next).replace(/<[\s\S]*?>/g, '');

    prev = parseFloat(prev);
    next = parseFloat(next);

    if (isNaN(prev)) prev = 9000;
    if (isNaN(next)) next = 9000;

    return ((prev < next) ? -1 : ((prev > next) ? 1 : 0));
};
// Only desc sort by numeric [rule name: only-numeric]
$.fn.dataTableExt.oSort['only-numeric-desc'] = function (prev, next) {
    prev = String(prev).replace(/<[\s\S]*?>/g, '');
    next = String(next).replace(/<[\s\S]*?>/g, '');

    prev = parseFloat(prev);
    next = parseFloat(next);

    if (isNaN(prev)) prev = -9000;
    if (isNaN(next)) next = -9000;

    return ((prev < next) ? 1 : ((prev > next) ? -1 : 0));
};
/**************************** DATATABLE ****************************/
