// Developed from the console.
// TODO !!! Re-tool this code for use in actually calculating average sleep time. 
var timesArr = [];
app.days.each(function(day) {
    var total = 0;
    _.each(day.toJSON(), function (time) {
        if (time === 'ASLEEP') {
            total +=15;
        }
    });
    console.log(total / 60);
    timesArr.push(total / 60)
});
var sum = 0;
for (var i=0; i<timesArr.length; i++) {
    sum += parseInt(timesArr[i], 10);
}
console.log(sum / timesArr.length);
