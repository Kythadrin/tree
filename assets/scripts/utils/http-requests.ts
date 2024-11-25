export async function httpGetRequest(url: string): Promise<Response> {
    try {
        return await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });
    } catch (error) {
        console.error('There was an error with the GET request:', error);
        throw error;
    }
}

export async function httpPostRequest(url: string, data: any): Promise<Response> {
    try {
        return await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });
    } catch (error) {
        console.error('There was an error with the POST request:', error);
        throw error;
    }
}

export async function httpDeleteRequest(url: string): Promise<Response> {
    try {
        return await fetch(url, {
            method: 'DELETE',
        });
    } catch (error) {
        console.error('There was an error with the POST request:', error);
        throw error;
    }
}

export async function httpPutRequest(url: string, data: any): Promise<Response> {
    try {
        return await fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });
    } catch (error) {
        console.error('There was an error with the PUT request:', error);
        throw error;
    }
}

export async function httpPatchRequest(url: string, data: any): Promise<Response> {
    try {
        return await fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });
    } catch (error) {
        console.error('There was an error with the PATCH request:', error);
        throw error;
    }
}
