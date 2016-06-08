var bridge = function (presenterPath) {
    // Default the poll rate to 1 second.
    if ( !this.pollRate ) {
        this.pollRate = 1000;
    }

    window.rhubarb.viewBridgeClasses.ViewBridge.apply(this, arguments);
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.ViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.onStateLoaded = function () {
    if (this.model.pollRate) {
        this.pollRate = this.model.pollRate * 1000;
    }

    // If we've been given a new background task status ID we need to start polling.
    if (this.model.backgroundTaskStatusId) {
        if ( !this.PreviousbackgroundTaskStatusId ||
             ( this.PreviousbackgroundTaskStatusId != this.model.backgroundTaskStatusId ) ) {

                this.PreviousbackgroundTaskStatusId = this.model.backgroundTaskStatusId;
                this.startPolling();
        }
    }
};

bridge.prototype.pollProgress = function () {

    // If we don't have a background task to poll, we shouldn't bother.
    if (!this.model.backgroundTaskStatusId) {
        return;
    }

    var self = this;

    this.raiseServerEvent("getProgress", function (response) {
        if (!response.isRunning) {

            clearInterval(self.pollInterval);

            self.raiseClientEvent("OnComplete");
            self.onComplete();
        }

        self.onProgressReported(response);
    });
};

bridge.prototype.startPolling = function () {

    var self = this;

    this.pollInterval = setInterval(function () {
        self.pollProgress();
    }, this.pollRate);
};

bridge.prototype.setBackgroundTaskStatusId = function (backgroundTaskStatusId) {
    this.model.backgroundTaskStatusId = backgroundTaskStatusId;
    this.pollProgress();
};

bridge.prototype.onProgressReported = function (progress) {

};

bridge.prototype.onComplete = function () {
};

window.rhubarb.viewBridgeClasses.BackgroundTaskViewBridge = bridge;