var bridge = function (presenterPath) {
    // Default the poll rate to 1 second.
    if ( !this.pollRate ) {
        this.pollRate = 1000;
    }

    window.rhubarb.viewBridgeClasses.HtmlViewBridge.apply(this, arguments);
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.HtmlViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.onStateLoaded = function () {
    if (this.model.pollRate) {
        this.pollRate = this.model.pollRate * 1000;
    }

    // If we've been given a new background task status ID we need to start polling.
    if (this.model.BackgroundTaskStatusID) {
        if ( !this.PreviousBackgroundTaskStatusID ||
             ( this.PreviousBackgroundTaskStatusID != this.model.BackgroundTaskStatusID ) ) {

                this.PreviousBackgroundTaskStatusID = this.model.BackgroundTaskStatusID;
                this.startPolling();
        }
    }
};

bridge.prototype.pollProgress = function () {

    // If we don't have a background task to poll, we shouldn't bother.
    if (!this.model.BackgroundTaskStatusID) {
        return;
    }

    var self = this;

    this.raiseServerEvent("GetProgress", function (response) {
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
    this.model.BackgroundTaskStatusID = backgroundTaskStatusId;
    this.pollProgress();
};

bridge.prototype.onProgressReported = function (progress) {

};

bridge.prototype.onComplete = function () {
};

window.rhubarb.viewBridgeClasses.BackgroundTaskViewBridge = bridge;