export async function httpGetRequest(url: string): Promise<any> {
    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (response.ok) {
            return await response.json();
        }
    } catch (error) {
        console.error('There was an error with the GET request:', error);
        throw error;
    }
}

export async function httpPostRequest(url: string, data: any): Promise<any> {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });

        if (response.ok) {
            return await response.json();
        }
    } catch (error) {
        console.error('There was an error with the POST request:', error);
        throw error;
    }
}
