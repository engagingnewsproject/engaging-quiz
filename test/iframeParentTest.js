var assert = chai.assert;
var expect = chai.expect;

describe('iframeParent', function() {

    before(function() {
        // runs before all tests in this block
        var saveEnpEmbedSite = false;
    });

    after(function() {
        // runs after all tests in this block
        var saveEnpEmbedSite = false;
    });

    beforeEach(function() {
        // runs before each test in this block

    });

    afterEach(function() {
        // runs after each test in this block

    });

    // test cases
    describe('receiveEnpIframeMessage', function() {

        describe('setEnpQuizHeight', function() {
            var setHeightSpy = sinon.spy(window, 'setEnpQuizHeight');
            var height = '123px';

            // setup a valid event on the request
            var event = {
                origin: 'http://local.quiz',
                data: {
                    site: 'http://local.quiz',
                    ab_test_id: '0',
                    quiz_id: '1',
                    action: 'setHeight',
                    height: height
                },
            };

            event.data = JSON.stringify(event.data);

            // make a fake event call
            var response = receiveEnpIframeMessage(event);

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


        describe('saveEnpEmbedSite', function() {
            var saveEnpEmbedsite = sinon.spy(window, 'saveEnpEmbedSite');

            // setup a valid event on the request
            var event = {
                origin: 'http://local.quiz',
                data: {
                    site: 'http://local.quiz',
                    ab_test_id: '0',
                    quiz_id: '1',
                    action: 'sendURL',

                },
            };

            event.data = JSON.stringify(event.data);

            // make a fake event call
            var response = receiveEnpIframeMessage(event);

            it('should call saveEnpEmbedSite', function() {
                // Chai + Sinon here.
                expect(saveEnpEmbedSite).to.have.been.called;
                // When creating a spy or stub to wrap a function, you'll want
                // to make sure you restore the original function back at the
                // end of your test case
                saveEnpEmbedsite.restore();
            });

            it('should return embed quiz id when set correctly', function() {
                console.log(response);
                // assert.equal(response.saveEnpEmbedSite, height);
            });




        });

    });
});
