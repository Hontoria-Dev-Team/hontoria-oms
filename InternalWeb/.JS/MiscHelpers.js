function formatCamelCase(str) {
    return str
        .replace(/([a-z])([A-Z])/g, '$1 $2')
        .replace(/^./, s => s.toUpperCase());
}

function getArrayDiff(arr1, arr2, key) {
    const set1 = new Set(arr1.map(item => item[key]));
    const set2 = new Set(arr2.map(item => item[key]));

    return [
        ...arr1.filter(item => !set2.has(item[key])),
        ...arr2.filter(item => !set1.has(item[key]))
    ];
}

function getDirectionalArrayDiff(arr1, arr2, key) {
    const set2 = new Set(arr2.map(item => item[key]));
    return arr1.filter(item => !set2.has(item[key]));
}

function getArrayInter(arr1, arr2, key) {
    const valuesInArr2 = new Set(arr2.map(item => item[key]));
    return arr1.filter(item => valuesInArr2.has(item[key]));
}
