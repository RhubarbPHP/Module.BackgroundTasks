var bridge = function (presenterPath)
{
    window.rhubarb.viewBridgeClasses.BackgroundTaskViewBridge.apply( this, arguments );
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.BackgroundTaskViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.onReady = function ()
{
    this.viewNode.style.display = 'none';
    this.progressNode = this.viewNode.querySelector( '.progress' );
    this.messageNode = this.viewNode.querySelector( '.message' );
};

bridge.prototype.onStart = function()
{
    this.viewNode.style.display = 'block';

    this.progressNode.style.width = "0%";
    this.messageNode.innerHTML = 'Please wait...';
};

bridge.prototype.onProgressReported = function(progress)
{
    this.viewNode.style.display = 'block';

    this.progressNode.style.width = progress.percentageComplete + "%";
    this.messageNode.innerHTML = progress.message;
};

bridge.prototype.onComplete = function(result)
{
    this.viewNode.style.display = 'none';
};

bridge.prototype.onFailed = function(result)
{
    this.viewNode.style.display = 'none';
};


window.rhubarb.viewBridgeClasses.BackgroundTaskProgressViewBridge = bridge;