const socket = io('http://localhost:3000');

let localStream;
let peerConnection;
let remoteSocketId = null;

const servers = {
    iceServers: [
        {
            urls: 'stun:stun.l.google.com:19302'
        }
    ]
};

async function iniciarWebRTC(){

    localStream =
    await navigator.mediaDevices.getUserMedia({
        audio: true,
        video: false
    });

    peerConnection =
    new RTCPeerConnection(servers);

    localStream.getTracks().forEach(track => {
        peerConnection.addTrack(track, localStream);
    });

    peerConnection.onicecandidate = (event) => {

        if(event.candidate){

            socket.emit('iceCandidate', {
                to: remoteSocketId,
                candidate: event.candidate
            });

        }

    };

}

async function crearOffer(){

    await iniciarWebRTC();

    const offer =
    await peerConnection.createOffer();

    await peerConnection.setLocalDescription(
        offer
    );

    socket.emit('offer', {
        to: remoteSocketId,
        offer: offer
    });

}

socket.on('offer', async (data) => {

    remoteSocketId = data.from;

    await iniciarWebRTC();

    await peerConnection.setRemoteDescription(
        new RTCSessionDescription(data.offer)
    );

    const answer =
    await peerConnection.createAnswer();

    await peerConnection.setLocalDescription(
        answer
    );

    socket.emit('answer', {
        to: remoteSocketId,
        answer: answer
    });

});

socket.on('answer', async (data) => {

    await peerConnection.setRemoteDescription(
        new RTCSessionDescription(data.answer)
    );

});

socket.on('iceCandidate', async (data) => {

    if(peerConnection){

        await peerConnection.addIceCandidate(
            new RTCIceCandidate(data.candidate)
        );

    }

});