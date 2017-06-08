var assert = chai.assert;
var expect = chai.expect;

describe('iframeParent', function() {

    before(function() {
        // runs before all tests in this block
    });

    after(function() {
        // runs after all tests in this block
    });

    beforeEach(function() {
        // runs before each test in this block

    });

    afterEach(function() {
        // runs after each test in this block

    });

    // test cases
    describe('receiveEnpIframeMessage', function() {

        var event = {
            origin: 'http://local.quiz',
            data: {
                site: 'http://local.quiz',
                ab_test_id: '0',
                quiz_id: '1',
            },
        };

        describe('setEnpQuizHeight', function() {

            var setHeightSpy = sinon.spy(enpIframe, 'setEnpQuizHeight');
            var height = '123px';
            var response = '';
            // set our extra event options
            event.data.action = 'setHeight';
            event.data.height = height;

            before(function(done) {

                event.data = JSON.stringify(event.data);

                // make a fake event call
                response = enpIframe.receiveEnpIframeMessage(event);

                // delay it a bit so we can wait for the response
                setTimeout(function(){
                      // complete the async before
                      return done();
                }, 50);
            });



            it('should call setEnpQuizHeight', function() {
                // Chai + Sinon here.
                expect(setHeightSpy).to.have.been.called;
                // When creating a spy or stub to wrap a function, you'll want
                // to make sure you restore the original function back at the
                // end of your test case
                setHeightSpy.restore();
            });

            it('should return height when set correctly', function() {
                expect(response.setEnpQuizHeight).to.equal(height);
            });

        });


        it('should set saveEnpEmbedSite equal to false initially', function() {
            expect(enpIframe.getSaveEmbedSiteComplete()).to.equal(false);
        });

        it('should set saveEnpEmbedQuiz to false initially', function() {
            expect(enpIframe.getSaveEmbedQuizComplete()).to.equal(false);
        });

        it('should set embedSiteID equal to false initially', function() {
            expect(enpIframe.getEmbedSiteID()).to.equal(false);
        });

        it('should set embedQuizID equal to false initially', function() {
            expect(enpIframe.getEmbedQuizID()).to.equal(false);
        });

        describe('saveEnpEmbedSite', function() {
            // Save Embed Site
            var saveEnpEmbedSiteSpy = sinon.spy(enpIframe, 'saveEnpEmbedSite');
            var enpHandleEmbedSiteResponseSpy = sinon.spy(enpIframe, 'enpHandleEmbedSiteResponse');
            // Save Embed Quiz
            var saveEnpEmbedQuizSpy = sinon.spy(enpIframe, 'saveEnpEmbedQuiz');
            var enpHandleEmbedQuizResponseSpy = sinon.spy(enpIframe, 'enpHandleEmbedQuizResponse');
            console.log(enpIframe.getEmbedSiteID());
            console.log(enpIframe.getEmbedQuizID());

            before(function(done){
                // setup a valid event on the request
                event.data.action = 'sendURL';

                event.data = JSON.stringify(event.data);
                // make a fake event call
                enpIframe.receiveEnpIframeMessage(event);

                // delay it a half second so we can wait for the response
                setTimeout(function(){
                      // complete the async before
                      return done();
                }, 400);
            });

            it('should call saveEnpEmbedSite', function() {
                // Chai + Sinon here.
                expect(saveEnpEmbedSiteSpy).to.have.been.called;
                // When creating a spy or stub to wrap a function, you'll want
                // to make sure you restore the original function back at the
                // end of your test case
                saveEnpEmbedSiteSpy.restore();
            });

            it('should call enpHandleEmbedSiteResponse', function() {
                // Chai + Sinon here.
                expect(enpHandleEmbedSiteResponseSpy).to.have.been.called;
                // When creating a spy or stub to wrap a function, you'll want
                // to make sure you restore the original function back at the
                // end of your test case
                enpHandleEmbedSiteResponseSpy.restore();
            });

            it('should call saveEnpEmbedQuiz', function() {
                // Chai + Sinon here.
                expect(saveEnpEmbedQuizSpy).to.have.been.called;
                // When creating a spy or stub to wrap a function, you'll want
                // to make sure you restore the original function back at the
                // end of your test case
                saveEnpEmbedQuizSpy.restore();
            });

            it('should call enpHandleEmbedQuizResponse', function() {
                // Chai + Sinon here.
                expect(enpHandleEmbedQuizResponseSpy).to.have.been.called;
                // When creating a spy or stub to wrap a function, you'll want
                // to make sure you restore the original function back at the
                // end of your test case
                enpHandleEmbedQuizResponseSpy.restore();
            });

            it('should only call saveEnpEmbedQuiz once', function() {
                expect(saveEnpEmbedQuizSpy).calledOnce;
            });

            it('should set saveEnpEmbedSite to true', function() {
                expect(enpIframe.getSaveEmbedSiteComplete()).to.equal(true);
            });

            it('should set saveEnpEmbedQuiz to true', function() {
                expect(enpIframe.getSaveEmbedQuizComplete()).to.equal(true);
            });

            it('should set embedSiteID equal to 2', function() {
                expect(enpIframe.getEmbedSiteID()).to.equal('2');
            });

            it('should set embedQuizID equal to 77', function() {
                expect(enpIframe.getEmbedQuizID()).to.equal('77');
            });
        });

    });
});
