// firebase-config.js
const firebaseConfig = {
apiKey: "AIzaSyDBhzwkW8JkRZtK_7GqTECGpMkGCxYeQmQ",
    authDomain: "coolfrog-1faea.firebaseapp.com",
    databaseURL: "https://coolfrog-1faea-default-rtdb.firebaseio.com",
    projectId: "coolfrog-1faea",
    storageBucket: "coolfrog-1faea.firebasestorage.app",
    messagingSenderId: "1021743120184",
    appId: "1:1021743120184:web:1155630157ee791e2b0bcd",
    measurementId: "G-Z71QHDPVPH"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const database = firebase.database();
