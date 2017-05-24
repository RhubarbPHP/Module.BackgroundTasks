var bridge = function (presenterPath) {
    window.rhubarb.viewBridgeClasses.ViewBridge.apply(this, arguments);
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.ViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.onProgressReported = function (progress) {
};

bridge.prototype.onComplete = function (result) {
};

bridge.prototype.onFailed = function () {
};

bridge.prototype.start = function(){

    var argumentsArray = [];

    // Get the arguments into a proper array while stripping any closure found to become a callback.
    argumentsArray.push("triggerTask");

    for (var i = 0; i < arguments.length; i++) {
        argumentsArray.push(arguments[i]);
    }

    var xmlhttp = this.raiseServerEvent.apply(this,argumentsArray);

    xmlhttp.onreadystatechange = function () {
        // Get the last line, if we've missed any we don't care - it's just a status
        // update.
        var lines = xmlhttp.responseText.trim().split("\n");

        if (lines.length>0 && lines[lines.length-1]){
            var progress = JSON.parse(lines[lines.length-1]);
            this.onProgressReported(progress);
            this.raiseClientEvent("OnProgressReported", progress);

            if (progress.status == "Complete"){
                this.onComplete(progress.result);
                this.raiseClientEvent("OnComplete", progress.result);
            } else if (progress.status == "Failed"){
                this.onFailed(progress.result);
                this.raiseClientEvent("OnFailed", progress.result);
            }
        }
    }.bind(this);
};


window.rhubarb.viewBridgeClasses.BackgroundTaskViewBridge = bridge;