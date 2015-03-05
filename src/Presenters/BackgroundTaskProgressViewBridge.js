var bridge = function (presenterPath)
{
    window.gcd.core.mvp.viewBridgeClasses.HtmlViewBridge.apply( this, arguments );

    // Default the poll rate to 1 second.
    this.pollRate = 1;

    if ( this.model.pollRate )
    {
        this.pollRate = this.model.pollRate;
    }

    this.progressNode = this.viewNode.querySelector( '.progress' );
    this.messageNode = this.viewNode.querySelector( '.message' );
};

bridge.prototype = new window.gcd.core.mvp.viewBridgeClasses.HtmlViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.attachEvents = function ()
{
    var self = this;

    setInterval( function()
    {
        self.pollProgress();
    }, this.pollRate );
};

bridge.prototype.pollProgress = function()
{
    var self = this;

    this.raiseServerEvent( "GetProgress", function( response )
    {
        self.progressNode.style = "width: " + response.percentageComplete + "%";
        self.messageNode.innerHTML = response.message;

        if ( response.percentageComplete >= 100 )
        {
            self.onComplete();
            self.raiseClientEvent( "OnComplete" );
        }
    });
};

bridge.prototype.onComplete = function()
{

};

window.gcd.core.mvp.viewBridgeClasses.BackgroundTaskProgressViewBridge = bridge;