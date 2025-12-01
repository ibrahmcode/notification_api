/**
 * Firebase Installation Request Helper
 * This file helps create installation requests for Firebase Cloud Messaging
 */

interface FirebaseConfig {
    apiKey: string;
    authDomain: string;
    projectId: string;
    storageBucket: string;
    messagingSenderId: string;
    appId: string;
}

interface InstallationRequest {
    fid?: string;
    authVersion: string;
    appId: string;
    sdkVersion: string;
}

export function createInstallationRequest(config: FirebaseConfig): InstallationRequest {
    return {
        authVersion: 'FIS_v2',
        appId: config.appId,
        sdkVersion: 'w:10.7.1'
    };
}

export async function registerInstallation(config: FirebaseConfig): Promise<any> {
    const endpoint = `https://firebaseinstallations.googleapis.com/v1/projects/${config.projectId}/installations`;
    
    const body = createInstallationRequest(config);
    
    const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'x-goog-api-key': config.apiKey
        },
        body: JSON.stringify(body)
    });
    
    if (!response.ok) {
        throw new Error(`Installation request failed: ${response.statusText}`);
    }
    
    return await response.json();
}

export default {
    createInstallationRequest,
    registerInstallation
};
