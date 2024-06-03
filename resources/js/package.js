function getListingExtra(from, to) {
    if (!config.category) {
        return 0
    }

    let add = 0
    Object.keys(config.category['banners']).forEach(indices => {
        indices.split(',').forEach(index => {
            if(!isNaN(index) && index <= to && index >= from) {
                add++
            }
        })
    })
    return add
}

Vue.prototype.getListingCount = function (count, pageSize) {
    let curCount = count % window.app.getListingSize(pageSize)
    return curCount + getListingExtra(0, curCount)
}

Vue.prototype.getListingSize = function(pageSize) {
    return pageSize - getListingExtra(0, pageSize)
}