import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import type { FormEvent } from 'react';

type Result = { email: string; valid: boolean };

export default function BulkEmailValidator({
    results,
    input,
}: {
    results: Result[];
    input: string;
}) {
    const form = useForm({ emails: input ?? '' });
    const [collapsed, setCollapsed] = useState(false);

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post('/bulk-email-validator', { preserveScroll: true });
    };

    return (
        <>
            <Head title="Bulk Email Validator" />
            <section className="legacy-panel">
                <div className="legacy-panel-title">
                    <span>Bulk Email Validator</span>
                    <div className="legacy-panel-tools">
                        <button
                            type="button"
                            className="minimize"
                            aria-label="Collapse email validator"
                            onClick={() => setCollapsed((value) => !value)}
                        />
                        <button
                            type="button"
                            className="refresh"
                            aria-label="Clear email validator"
                            onClick={() => form.setData('emails', '')}
                        />
                    </div>
                </div>
                <form
                    onSubmit={submit}
                    className={
                        collapsed
                            ? 'legacy-panel-body legacy-email-tool hidden'
                            : 'legacy-panel-body legacy-email-tool'
                    }
                >
                    <div className="legacy-instructions">
                        <h2>Instructions:</h2>
                        <p>
                            Step 1: Add all the emails to the textbox, one email
                            on each line (basically copy/paste from the
                            spreadsheet into the textbox)
                        </p>
                        <p>Step 2: Press the validate emails button</p>
                        <p>
                            Step 3: The script will validate the list of emails
                            and advise which ones are incorrect
                        </p>
                    </div>
                    <textarea
                        value={form.data.emails}
                        onChange={(e) => form.setData('emails', e.target.value)}
                        placeholder="Add the list of emails here. One email per line."
                    />
                    <button className="legacy-action-button" type="submit">
                        Validate Emails
                    </button>
                    {results.length > 0 && (
                        <div className="legacy-results">
                            {results.map((result) => (
                                <p
                                    key={result.email}
                                    className={
                                        result.valid ? 'valid' : 'invalid'
                                    }
                                >
                                    {result.email} -{' '}
                                    {result.valid ? 'Valid' : 'Invalid'}
                                </p>
                            ))}
                        </div>
                    )}
                </form>
            </section>
        </>
    );
}

BulkEmailValidator.layout = {
    breadcrumbs: [
        { title: 'Bulk Email Validator', href: '/bulk-email-validator' },
    ],
};
