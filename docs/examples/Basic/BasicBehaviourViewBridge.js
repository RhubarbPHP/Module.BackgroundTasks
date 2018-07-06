rhubarb.vb.create('BasicBehaviourViewBridge', function(){
   return {
        onReady: function(){
            var aTag = this.viewNode.querySelector('a');
            var progressLabel = this.viewNode.querySelector('.progress');
            var runner = this.findChildViewBridge('task-runner');

            runner.attachClientEventHandler('OnProgressReported', function(progress){
                progressLabel.innerHTML = progress.percentageComplete + ' ' + progress.message;
            });

            runner.attachClientEventHandler('OnComplete', function(response){
                alert(response);
                aTag.hidden = false;
                progressLabel.innerHTML = "";
            });

            aTag.addEventListener('click', function(event){
                runner.start();
                aTag.hidden = true;

                event.preventDefault();
            });
        }
   };
});